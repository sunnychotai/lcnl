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
        $HARD_PER_RUN_CAP = 5;          // cron every 1 min => 5/run => 50/10m
        $TEN_MIN_CAP = 50;         // Fasthosts limit in any rolling 10 minutes
        $DAILY_CAP = 1000;       // Fasthosts limit in rolling 24 hours
        $AUTO_PAUSE_AFTER_FAIL = 5;         // consecutive failures to trigger pause
        $AUTO_PAUSE_MINUTES = 10;         // pause duration
        $BACKOFF_ABORT_AFTER = 3;          // abort run after N backoff-worthy SMTP errors
        $BACKOFF_STEP_MS = 2000;       // +2s per backoff step within this run

        $cache = cache();
        $pauseKey = 'email_queue_paused_until';

        /* ---------------------------------------------------------
         * TEST MODE (redirect all to one address)
         * php spark emails:send --test[=address]
         * --------------------------------------------------------- */
        $testParam = CLI::getOption('test'); // null (off), "" (on -> default), "email@"
        $isTestMode = $testParam !== null;
        $testAddress = $isTestMode
            ? (trim((string) $testParam) ?: 'sunnychotai@me.com')
            : null;

        if ($isTestMode) {
            CLI::write(str_repeat('=', 45), 'yellow');
            CLI::write(" TEST MODE ENABLED", 'yellow');
            CLI::write(" Redirecting all emails to: {$testAddress}", 'yellow');
            CLI::write(str_repeat('=', 45), 'yellow');
        }

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
            'backoff' => [],
            'paused' => false,
            'test_mode' => $isTestMode,
            'test_email' => $testAddress,
        ];

        try {
            /* ---------------------------------------------------------
             * PAUSE CHECK
             * --------------------------------------------------------- */
            $pausedUntil = $cache->get($pauseKey);
            $nowTs = time();
            if (is_numeric($pausedUntil) && $pausedUntil > $nowTs) {
                $untilStr = date('Y-m-d H:i:s', (int) $pausedUntil);
                CLI::write("Email sending is paused until {$untilStr}. Exiting.", 'yellow');
                $result['paused'] = true;
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            $cfg = config('EmailQueue');
            $emailCfg = config('Email');

            $batch = (int) (CLI::getOption('batch') ?? $cfg->batchSize ?? 50);
            if ($batch < 1)
                $batch = 1;

            $model = new EmailQueueModel();

            /* ---------------------------------------------------------
             * ROLLING LIMITS
             * --------------------------------------------------------- */
            $tsNow = time();
            $tenMinAgo = date('Y-m-d H:i:s', $tsNow - (10 * 60));
            $twentyFourAgo = date('Y-m-d H:i:s', $tsNow - (24 * 60 * 60));

            // 24-hour limit
            $sentLast24h = $model->where('status', 'sent')
                ->where('sent_at >=', $twentyFourAgo)
                ->countAllResults();
            $model->resetQuery();

            $remaining24h = max(0, $DAILY_CAP - $sentLast24h);

            // 10-minute limit
            $sentLast10 = $model->where('status', 'sent')
                ->where('sent_at >=', $tenMinAgo)
                ->countAllResults();
            $model->resetQuery();

            $remaining10 = max(0, $TEN_MIN_CAP - $sentLast10);

            // determine per-run cap: never exceed hard cap, remaining10, remaining24h, batch
            $take = min($batch, $HARD_PER_RUN_CAP, $remaining10, $remaining24h);

            $result['limit_info'] = [
                'sent_last_10_min' => $sentLast10,
                'remaining_10_min' => $remaining10,
                'sent_last_24h' => $sentLast24h,
                'remaining_24h' => $remaining24h,
                'hard_per_run_cap' => $HARD_PER_RUN_CAP,
                'batch_requested' => $batch,
                'take_effective' => $take,
            ];

            if ($remaining24h <= 0) {
                CLI::write("24-hour limit (max {$DAILY_CAP}) reached. Exiting.", 'yellow');
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            if ($remaining10 <= 0) {
                CLI::write("10-minute limit (max {$TEN_MIN_CAP}) reached. Exiting.", 'yellow');
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            if ($take <= 0) {
                CLI::write("Effective take is 0 due to limits. Exiting.", 'yellow');
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            /* ---------------------------------------------------------
             * FETCH PENDING ROWS
             * --------------------------------------------------------- */
            $rows = $model->where('status', 'pending')
                ->groupStart()
                ->where('scheduled_at', null)
                ->orWhere('scheduled_at <=', date('Y-m-d H:i:s'))
                ->groupEnd()
                ->orderBy('priority', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll($take);

            if (empty($rows)) {
                $msg = "No emails to send.";
                CLI::write($msg, 'green');
                $result['errors'][] = $msg; // note only
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            $email = \Config\Services::email();

            $processed = 0;
            $sent = 0;
            $failed = 0;
            $consecutiveFailures = 0;
            $backoffErrors = 0;
            $dynamicDelayMs = (int) ($cfg->sendDelayMs ?? 0);

            foreach ($rows as $row) {
                // Validate recipient
                $toEmail = trim((string) ($row['to_email'] ?? ''));
                if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                    $model->update($row['id'], [
                        'status' => 'invalid',
                        'last_error' => 'INVALID_EMAIL: ' . ($toEmail ?: '(empty)'),
                        'sent_at' => null,
                    ]);
                    $failed++;
                    $result['errors'][] = "Row #{$row['id']} invalid email: " . ($toEmail ?: '(empty)');
                    // do not count toward consecutive SMTP failures (not an SMTP error)
                    continue;
                }

                // Mark sending
                $model->update($row['id'], [
                    'status' => 'sending',
                    'attempts' => (int) ($row['attempts'] ?? 0) + 1,
                ]);

                try {
                    // DRY RUN short-circuit
                    if (!($cfg->sendEnabled ?? true) || ($cfg->dryRun ?? false)) {
                        $model->update($row['id'], [
                            'status' => 'sent',
                            'sent_at' => date('Y-m-d H:i:s'),
                            'last_error' => 'DRY-RUN',
                        ]);
                        $sent++;
                        $processed++;
                        $consecutiveFailures = 0; // reset on success
                        if ($dynamicDelayMs > 0) {
                            usleep($dynamicDelayMs * 1000);
                        }
                        // check if we hit take
                        if ($processed >= $take)
                            break;
                        continue;
                    }

                    // Build email
                    $email->clear(true);
                    $email->setFrom($emailCfg->fromEmail, $emailCfg->fromName);

                    if ($isTestMode) {
                        $email->setTo($testAddress);
                        $email->setSubject('[TEST] ' . $row['subject']);
                        $banner = "<div style=\"padding:10px;background:#ffdddd;color:#a30000;border:1px solid #ffaaaa;margin-bottom:15px;font-weight:bold;\">TEST MODE — Originally intended for {$toEmail}</div>";
                        if (!empty($row['body_html'])) {
                            $email->setMessage($banner . $row['body_html']);
                        }
                        if (!empty($row['body_text'])) {
                            $email->setAltMessage("TEST MODE — Originally intended for {$toEmail}\n\n" . $row['body_text']);
                        }
                    } else {
                        $email->setTo($toEmail, $row['to_name'] ?? null);
                        $email->setSubject($row['subject']);
                        if (!empty($row['body_html'])) {
                            $email->setMessage($row['body_html']);
                        }
                        if (!empty($row['body_text'])) {
                            $email->setAltMessage($row['body_text']);
                        }
                    }

                    if (!empty($row['headers_json'])) {
                        $headers = json_decode($row['headers_json'], true) ?: [];
                        foreach ($headers as $k => $v) {
                            $email->setHeader($k, $v);
                        }
                    }

                    // Send
                    if ($email->send()) {
                        $model->update($row['id'], [
                            'status' => 'sent',
                            'sent_at' => date('Y-m-d H:i:s'),
                            'last_error' => null,
                        ]);
                        $sent++;
                        $processed++;
                        $consecutiveFailures = 0; // reset on success
                    } else {
                        $err = $email->printDebugger(['headers', 'subject', 'body']);
                        $model->update($row['id'], [
                            'status' => 'failed',
                            'last_error' => substr($err, 0, 65535),
                        ]);
                        $failed++;
                        $consecutiveFailures++;
                        // Backoff-worthy?
                        if ($this->isBackoffWorthy($err)) {
                            $backoffErrors++;
                            $dynamicDelayMs += $BACKOFF_STEP_MS; // increase delay for subsequent sends in this run
                            $result['backoff'][] = [
                                'row_id' => $row['id'],
                                'error' => substr($err, 0, 200),
                                'delay_ms_now' => $dynamicDelayMs,
                            ];
                            // Abort early if too many SMTP-level issues
                            if ($backoffErrors >= $BACKOFF_ABORT_AFTER) {
                                CLI::write("Too many SMTP/backoff errors ({$backoffErrors}). Aborting this run early.", 'yellow');
                                break;
                            }
                        }
                    }

                    // Delay between sends (base + dynamic backoff)
                    if ($dynamicDelayMs > 0) {
                        usleep($dynamicDelayMs * 1000);
                    }

                    // STOP if we reached our effective take
                    if ($processed >= $take)
                        break;

                    // AUTO-PAUSE CHECK
                    if ($consecutiveFailures >= $AUTO_PAUSE_AFTER_FAIL) {
                        $untilTs = time() + ($AUTO_PAUSE_MINUTES * 60);
                        $cache->save($pauseKey, $untilTs, $AUTO_PAUSE_MINUTES * 60);
                        $untilStr = date('Y-m-d H:i:s', $untilTs);
                        CLI::write("Auto-pause triggered after {$consecutiveFailures} consecutive failures. Paused until {$untilStr}.", 'red');
                        $result['paused'] = true;
                        break;
                    }

                } catch (\Throwable $e) {
                    $model->update($row['id'], [
                        'status' => 'failed',
                        'last_error' => substr($e->getMessage(), 0, 65535),
                    ]);
                    $failed++;
                    $consecutiveFailures++;

                    // Backoff on throwable too
                    $dynamicDelayMs += $BACKOFF_STEP_MS;
                    $result['backoff'][] = [
                        'row_id' => $row['id'],
                        'error' => substr($e->getMessage(), 0, 200),
                        'delay_ms_now' => $dynamicDelayMs,
                    ];

                    if ($consecutiveFailures >= $AUTO_PAUSE_AFTER_FAIL) {
                        $untilTs = time() + ($AUTO_PAUSE_MINUTES * 60);
                        $cache->save($pauseKey, $untilTs, $AUTO_PAUSE_MINUTES * 60);
                        $untilStr = date('Y-m-d H:i:s', $untilTs);
                        CLI::write("Auto-pause triggered after {$consecutiveFailures} consecutive failures. Paused until {$untilStr}.", 'red');
                        $result['paused'] = true;
                        break;
                    }
                }
            }

            $result['processed'] = $processed;
            $result['sent'] = $sent;
            $result['failed'] = $failed;

            CLI::write("Emails processed: {$processed} | Sent: {$sent} | Failed: {$failed}", 'green');

            // Determine final status
            if ($failed === 0) {
                $status = 'success';
            } elseif ($sent > 0) {
                $status = 'partial';
            } else {
                $status = 'error';
            }

        } catch (\Throwable $e) {
            $result['errors'][] = $e->getMessage();
            $status = 'error';
            CLI::error("Fatal error: " . $e->getMessage());
        }

        /* ---------------------------------------------------------
         * CRON LOGGING – END
         * --------------------------------------------------------- */
        $this->writeCronLog($logModel, $jobName, $status, $result, $started);
    }

    private function isBackoffWorthy(string $err): bool
    {
        $e = strtolower($err);
        return (
            strpos($e, '421') !== false ||          // service not available / temp fail
            strpos($e, '451') !== false ||          // local error / temp fail
            strpos($e, '4.3.2') !== false ||        // shutting down / temp fail
            strpos($e, 'unable to send data: auth login') !== false ||
            strpos($e, 'authentication failed') !== false ||
            strpos($e, '535') !== false             // auth error
        );
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
