<?php

namespace App\Commands;

use App\Models\EmailQueueModel;
use App\Models\CronLogModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SendQueuedEmails extends BaseCommand
{
    protected $group = 'Email';
    protected $name = 'emails:send';
    protected $description = 'Send queued emails with Fasthosts-safe throttling, backoff, and auto-pause.';

    public function run(array $params)
    {
        /* ---------------------------------------------------------
         * CONFIG / CONSTANTS (Fasthosts-safe)
         * --------------------------------------------------------- */
        $HARD_PER_RUN_CAP      = 5;
        $TEN_MIN_CAP           = 50;
        $DAILY_CAP             = 950;
        $AUTO_PAUSE_AFTER_FAIL = 5;
        $AUTO_PAUSE_MINUTES    = 10;
        $BACKOFF_ABORT_AFTER   = 3;
        $BACKOFF_STEP_MS       = 2000;

        $cache    = cache();
        $pauseKey = 'email_queue_paused_until';

        /* ---------------------------------------------------------
         * TEST MODE
         * --------------------------------------------------------- */
        $testParam   = CLI::getOption('test');
        $isTestMode  = $testParam !== null;
        $testAddress = $isTestMode
            ? (trim((string) $testParam) ?: 'sunnychotai@me.com')
            : null;

        /* ---------------------------------------------------------
         * CRON LOGGING – START
         * --------------------------------------------------------- */
        $logModel = new CronLogModel();
        $jobName  = 'emails:send';
        $started  = date('Y-m-d H:i:s');

        $batchRequested = (int) (CLI::getOption('batch') ?? config('EmailQueue')->batchSize ?? 50);

        $result = [
            'processed'   => 0,
            'sent'        => 0,
            'failed'      => 0,
            'errors'      => [],
            'limit_info'  => [
                'sent_last_10_min'   => 0,
                'remaining_10_min'   => 0,
                'sent_last_24h'      => 0,
                'remaining_24h'      => 0,
                'hard_per_run_cap'   => $HARD_PER_RUN_CAP,
                'batch_requested'    => $batchRequested,
                'take_effective'     => 0,
            ],
            'backoff'     => [],
            'paused'      => false,
            'test_mode'   => $isTestMode,
            'test_email'  => $testAddress,
        ];

        try {
            /* ---------------------------------------------------------
             * PAUSE CHECK
             * --------------------------------------------------------- */
            $pausedUntil = $cache->get($pauseKey);
            if (is_numeric($pausedUntil) && $pausedUntil > time()) {
                $result['paused'] = true;
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            $cfg      = config('EmailQueue');
            $emailCfg = config('Email');
            $model    = new EmailQueueModel();

            /* ---------------------------------------------------------
             * ROLLING LIMITS
             * --------------------------------------------------------- */
            $nowTs        = time();
            $tenMinAgo    = date('Y-m-d H:i:s', $nowTs - 600);
            $twentyFourAgo = date('Y-m-d H:i:s', $nowTs - 86400);

            $sentLast24h = $model
                ->where('status', 'sent')
                ->where('sent_at >=', $twentyFourAgo)
                ->countAllResults(false);

            $model->resetQuery();

            $sentLast10 = $model
                ->where('status', 'sent')
                ->where('sent_at >=', $tenMinAgo)
                ->countAllResults(false);

            $model->resetQuery();

            $remaining24h = max(0, $DAILY_CAP - $sentLast24h);
            $remaining10  = max(0, $TEN_MIN_CAP - $sentLast10);

            $take = min(
                max(1, $batchRequested),
                $HARD_PER_RUN_CAP,
                $remaining10,
                $remaining24h
            );

            $result['limit_info'] = [
                'sent_last_10_min'   => $sentLast10,
                'remaining_10_min'   => $remaining10,
                'sent_last_24h'      => $sentLast24h,
                'remaining_24h'      => $remaining24h,
                'hard_per_run_cap'   => $HARD_PER_RUN_CAP,
                'batch_requested'    => $batchRequested,
                'take_effective'     => $take,
            ];

            if ($take <= 0) {
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            /* ---------------------------------------------------------
             * FETCH PENDING
             * --------------------------------------------------------- */
            $rows = $model->where('status', 'pending')
                ->groupStart()
                ->where('scheduled_at', null)
                ->orWhere('scheduled_at <=', date('Y-m-d H:i:s'))
                ->groupEnd()
                ->orderBy('priority', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll($take);

            if (!$rows) {
                $result['errors'][] = 'No emails to send';
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            $email = service('email');

            $processed = $sent = $failed = 0;
            $consecutiveFailures = 0;
            $backoffErrors = 0;
            $dynamicDelayMs = (int) ($cfg->sendDelayMs ?? 0);

            foreach ($rows as $row) {
                $toEmail = trim((string) ($row['to_email'] ?? ''));

                if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                    $model->update($row['id'], [
                        'status'     => 'invalid',
                        'last_error' => 'INVALID_EMAIL',
                    ]);
                    $failed++;
                    continue;
                }

                $model->update($row['id'], [
                    'status'   => 'sending',
                    'attempts' => ((int) $row['attempts']) + 1,
                ]);

                try {
                    if (!($cfg->sendEnabled ?? true) || ($cfg->dryRun ?? false)) {
                        $model->update($row['id'], [
                            'status'   => 'sent',
                            'sent_at' => date('Y-m-d H:i:s'),
                            'last_error' => 'DRY-RUN',
                        ]);
                        $sent++;
                        $processed++;
                        continue;
                    }

                    $email->clear(true);
                    $email->setFrom($emailCfg->fromEmail, $emailCfg->fromName);

                    if ($isTestMode) {
                        $email->setTo($testAddress);
                        $email->setSubject('[TEST] ' . $row['subject']);
                        $email->setMessage($row['body_html'] ?? '');
                    } else {
                        $email->setTo($toEmail, $row['to_name'] ?? null);
                        $email->setSubject($row['subject']);
                        $email->setMessage($row['body_html'] ?? '');
                        if (!empty($row['body_text'])) {
                            $email->setAltMessage($row['body_text']);
                        }
                    }

                    if ($email->send()) {
                        $model->update($row['id'], [
                            'status'   => 'sent',
                            'sent_at' => date('Y-m-d H:i:s'),
                        ]);
                        $sent++;
                        $processed++;
                        $consecutiveFailures = 0;
                    } else {
                        throw new \RuntimeException($email->printDebugger());
                    }
                } catch (\Throwable $e) {
                    $model->update($row['id'], [
                        'status'     => 'failed',
                        'last_error' => substr($e->getMessage(), 0, 65535),
                    ]);

                    $failed++;
                    $consecutiveFailures++;
                    $dynamicDelayMs += $BACKOFF_STEP_MS;

                    $result['backoff'][] = [
                        'row_id' => $row['id'],
                        'error'  => substr($e->getMessage(), 0, 200),
                        'delay_ms_now' => $dynamicDelayMs,
                    ];

                    if ($consecutiveFailures >= $AUTO_PAUSE_AFTER_FAIL) {
                        $cache->save(
                            $pauseKey,
                            time() + ($AUTO_PAUSE_MINUTES * 60),
                            $AUTO_PAUSE_MINUTES * 60
                        );
                        $result['paused'] = true;
                        break;
                    }
                }

                if ($dynamicDelayMs > 0) {
                    usleep($dynamicDelayMs * 1000);
                }

                if ($processed >= $take) {
                    break;
                }
            }

            $result['processed'] = $processed;
            $result['sent']      = $sent;
            $result['failed']    = $failed;

            $status = $failed === 0 ? 'success' : ($sent > 0 ? 'partial' : 'error');
        } catch (\Throwable $e) {
            $result['errors'][] = $e->getMessage();
            $status = 'error';
        }

        $this->writeCronLog($logModel, $jobName, $status, $result, $started);
    }

    private function writeCronLog(
        CronLogModel $logModel,
        string $jobName,
        string $status,
        array $result,
        string $started
    ): void {
        $logModel->insert([
            'job_name'    => $jobName,
            'status'      => $status,
            'summary'     => json_encode($result, JSON_PRETTY_PRINT),
            'started_at'  => $started,
            'finished_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
