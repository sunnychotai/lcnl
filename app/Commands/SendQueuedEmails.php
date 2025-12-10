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
    protected $description = 'Send queued emails in batches with daily and per-minute rate limits.';

    public function run(array $params)
    {
        /* ---------------------------------------------------------
         * CRON LOGGING – START
         * --------------------------------------------------------- */
        $logModel = new CronLogModel();
        $jobName = 'emails:send';
        $started = date('Y-m-d H:i:s');

        $result = [
            'processed' => 0,
            'sent' => 0,
            'failed' => 0,
            'errors' => [],
            'limit_info' => [],
        ];

        try {

            $cfg = config('EmailQueue');
            $emailCfg = config('Email');
            $batch = (int) (CLI::getOption('batch') ?? $cfg->batchSize);
            $now = date('Y-m-d H:i:s');

            $model = new EmailQueueModel();

            /* ---------------------------------------------------------
             * DAILY LIMIT CHECK
             * --------------------------------------------------------- */
            $midnight = date('Y-m-d 00:00:00');
            $sentToday = $model->where('status', 'sent')
                ->where('sent_at >=', $midnight)
                ->countAllResults();

            $remainingDaily = max(0, $cfg->dailyLimit - $sentToday);

            $result['limit_info']['sent_today'] = $sentToday;
            $result['limit_info']['remaining_daily'] = $remainingDaily;

            if ($remainingDaily <= 0) {
                $msg = "Daily limit reached. Exiting.";
                CLI::write($msg, 'yellow');
                $result['errors'][] = $msg;
                $status = 'success'; // No job error, just no more allowed
                $this->writeCronLog($logModel, $jobName, $status, $result, $started);
                return;
            }

            /* ---------------------------------------------------------
             * PER-MINUTE LIMIT CHECK
             * --------------------------------------------------------- */
            $oneMinuteAgo = date('Y-m-d H:i:s', strtotime('-1 minute'));
            $sentLastMinute = $model->where('status', 'sent')
                ->where('sent_at >=', $oneMinuteAgo)
                ->countAllResults();

            $remainingMinute = max(0, $cfg->perMinuteLimit - $sentLastMinute);

            $result['limit_info']['sent_last_minute'] = $sentLastMinute;
            $result['limit_info']['remaining_per_minute'] = $remainingMinute;

            if ($remainingMinute <= 0) {
                $msg = "Per-minute rate limit reached. Exiting.";
                CLI::write($msg, 'yellow');
                $result['errors'][] = $msg;
                $status = 'success';
                $this->writeCronLog($logModel, $jobName, $status, $result, $started);
                return;
            }

            /* ---------------------------------------------------------
             * DETERMINE TAKE LIMIT
             * --------------------------------------------------------- */
            $take = min($batch, $remainingDaily, $remainingMinute);

            $rows = $model->where('status', 'pending')
                ->groupStart()
                ->where('scheduled_at', null)
                ->orWhere('scheduled_at <=', $now)
                ->groupEnd()
                ->orderBy('priority', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll($take);

            if (empty($rows)) {
                $msg = "No emails to send.";
                CLI::write($msg, 'green');
                $result['errors'][] = $msg; // not an error, but note it
                $status = 'success';
                $this->writeCronLog($logModel, $jobName, $status, $result, $started);
                return;
            }

            $email = \Config\Services::email();

            $processed = 0;
            $sent = 0;
            $failed = 0;

            foreach ($rows as $row) {
                $model->update($row['id'], [
                    'status' => 'sending',
                    'attempts' => (int) $row['attempts'] + 1
                ]);

                try {

                    if ($cfg->dryRun || !$cfg->sendEnabled) {
                        $model->update($row['id'], [
                            'status' => 'sent',
                            'sent_at' => date('Y-m-d H:i:s'),
                            'last_error' => 'DRY-RUN',
                        ]);
                        $sent++;
                    } else {

                        $email->clear(true);
                        $email->setFrom($emailCfg->fromEmail, $emailCfg->fromName);
                        $email->setTo($row['to_email'], $row['to_name'] ?? null);
                        $email->setSubject($row['subject']);

                        if (!empty($row['body_html'])) {
                            $email->setMessage($row['body_html']);
                        }
                        if (!empty($row['body_text'])) {
                            $email->setAltMessage($row['body_text']);
                        }

                        if (!empty($row['headers_json'])) {
                            $headers = json_decode($row['headers_json'], true) ?: [];
                            foreach ($headers as $k => $v) {
                                $email->setHeader($k, $v);
                            }
                        }

                        if ($email->send()) {
                            $model->update($row['id'], [
                                'status' => 'sent',
                                'sent_at' => date('Y-m-d H:i:s'),
                                'last_error' => null,
                            ]);
                            $sent++;
                        } else {
                            $err = $email->printDebugger(['headers', 'subject', 'body']);
                            $model->update($row['id'], [
                                'status' => 'failed',
                                'last_error' => substr($err, 0, 65535),
                            ]);
                            $failed++;
                        }
                    }

                    $processed++;

                    /* MICRO DELAY */
                    if (!empty($cfg->sendDelayMs) && $cfg->sendDelayMs > 0) {
                        usleep($cfg->sendDelayMs * 1000);
                    }

                    if ($processed >= $take)
                        break;

                } catch (\Throwable $e) {
                    $model->update($row['id'], [
                        'status' => 'failed',
                        'last_error' => substr($e->getMessage(), 0, 65535),
                    ]);
                    $result['errors'][] = $e->getMessage();
                    $failed++;
                }
            }

            $result['processed'] = $processed;
            $result['sent'] = $sent;
            $result['failed'] = $failed;

            CLI::write("Emails processed: {$processed} | Sent: {$sent} | Failed: {$failed}", 'green');

            /* Determine final status */
            if ($failed === 0) {
                $status = 'success';
            } elseif ($sent > 0) {
                $status = 'partial';
            } else {
                $status = 'error';
            }

        } catch (\Throwable $e) {

            // FATAL FAILURE
            $result['errors'][] = $e->getMessage();
            $status = 'error';
            CLI::error("Fatal error: " . $e->getMessage());

        }

        /* ---------------------------------------------------------
         * CRON LOGGING – END
         * --------------------------------------------------------- */
        $this->writeCronLog($logModel, $jobName, $status, $result, $started);
    }

    private function writeCronLog(CronLogModel $logModel, string $jobName, string $status, array $result, string $started)
    {
        $logModel->insert([
            'job_name' => $jobName,
            'status' => $status,
            'summary' => json_encode($result, JSON_PRETTY_PRINT),
            'started_at' => $started,
            'finished_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
