<?php

namespace App\Commands;

use App\Models\EmailQueueModel;
use App\Models\CronLogModel;
use App\Models\MemberModel;
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
        $HARD_PER_RUN_CAP = 5;
        $TEN_MIN_CAP = 50;
        $DAILY_CAP = 950;
        $AUTO_PAUSE_AFTER_FAIL = 5;
        $AUTO_PAUSE_MINUTES = 10;
        $BACKOFF_ABORT_AFTER = 3;
        $BACKOFF_STEP_MS = 2000;

        $cache = cache();
        $pauseKey = 'email_queue_paused_until';

        /* ---------------------------------------------------------
         * TEST MODE
         * --------------------------------------------------------- */
        $testParam = CLI::getOption('test');
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
            'limit_info' => [
                'sent_last_10_min'   => 0,
                'remaining_10_min'   => 0,
                'sent_last_24h'      => 0,
                'remaining_24h'      => 0,
                'hard_per_run_cap'   => $HARD_PER_RUN_CAP,
                'batch_requested'    => 0,
                'take_effective'     => 0,
            ],
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
            if (is_numeric($pausedUntil) && $pausedUntil > time()) {
                $remainingMinutes = ceil(($pausedUntil - time()) / 60);
                CLI::write("⏸ Email sending paused for {$remainingMinutes} more minutes.", 'yellow');
                $result['paused'] = true;
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            $cfg = config('EmailQueue');
            $emailCfg = config('Email');

            $batch = (int) ($cfg->batchSize ?? 50);
            if ($batch < 1) $batch = 1;

            $model = new EmailQueueModel();

            /* ---------------------------------------------------------
             * ROLLING LIMITS
             * --------------------------------------------------------- */
            $now = time();
            $tenMinAgo = date('Y-m-d H:i:s', $now - 600);
            $dayAgo = date('Y-m-d H:i:s', $now - 86400);

            $sent24h = $model->where('status', 'sent')->where('sent_at >=', $dayAgo)->countAllResults();
            $model->resetQuery();

            $sent10m = $model->where('status', 'sent')->where('sent_at >=', $tenMinAgo)->countAllResults();
            $model->resetQuery();

            $remaining24h = max(0, $DAILY_CAP - $sent24h);
            $remaining10m = max(0, $TEN_MIN_CAP - $sent10m);

            $take = min(
                $batch,
                $HARD_PER_RUN_CAP,
                $remaining24h,
                $remaining10m
            );

            // Update limit_info with actual values
            $result['limit_info'] = [
                'sent_last_10_min'   => $sent10m,
                'remaining_10_min'   => $remaining10m,
                'sent_last_24h'      => $sent24h,
                'remaining_24h'      => $remaining24h,
                'hard_per_run_cap'   => $HARD_PER_RUN_CAP,
                'batch_requested'    => $batch,
                'take_effective'     => $take,
            ];

            // Display rate limit status
            CLI::write('─────────────────────────────────────', 'light_gray');
            CLI::write('📊 Rate Limit Status:', 'cyan');
            CLI::write('  10-min window: ' . $sent10m . '/' . $TEN_MIN_CAP . ' (remaining: ' . $remaining10m . ')', 'light_gray');
            CLI::write('  24-hour window: ' . $sent24h . '/' . $DAILY_CAP . ' (remaining: ' . $remaining24h . ')', 'light_gray');
            CLI::write('  Batch size: ' . $batch . ' | Per-run cap: ' . $HARD_PER_RUN_CAP, 'light_gray');
            CLI::write('  Effective take: ' . $take . ' emails', $take > 0 ? 'green' : 'yellow');
            CLI::write('─────────────────────────────────────', 'light_gray');

            if ($take <= 0) {
                if ($sent24h >= $DAILY_CAP) {
                    CLI::write('✗ Daily limit reached (' . $DAILY_CAP . ' emails/24h)', 'red');
                    CLI::write('  Next reset: ~' . (24 - (int)((time() - strtotime($dayAgo)) / 3600)) . ' hours', 'yellow');
                } elseif ($sent10m >= $TEN_MIN_CAP) {
                    CLI::write('✗ 10-minute limit reached (' . $TEN_MIN_CAP . ' emails/10min)', 'red');
                    CLI::write('  Next window: ~' . (10 - (int)((time() - strtotime($tenMinAgo)) / 60)) . ' minutes', 'yellow');
                } else {
                    CLI::write('✗ Rate limits reached', 'yellow');
                }
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            /* ---------------------------------------------------------
             * FETCH PENDING EMAILS
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
                CLI::write("✓ No emails to send. Queue is empty.", 'green');
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            CLI::write("📧 Processing " . count($rows) . " email(s)...", 'green');

            $email = service('email');
            $consecutiveFailures = 0;

            foreach ($rows as $row) {

                $toEmail = trim((string) ($row['to_email'] ?? ''));
                if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                    $model->update($row['id'], [
                        'status' => 'invalid',
                        'last_error' => 'INVALID_EMAIL'
                    ]);
                    $result['failed']++;
                    CLI::write("  ✗ Invalid email: " . $toEmail, 'red');
                    continue;
                }

                $model->update($row['id'], [
                    'status' => 'sending',
                    'attempts' => (int) ($row['attempts'] ?? 0) + 1,
                ]);

                /* ---------------------------------------------------------
                 * MEMBER ACTIVATION TOKEN (SEND TIME)
                 * --------------------------------------------------------- */
                if (($row['type'] ?? null) === 'member_activation') {

                    $memberId = (int) ($row['related_id'] ?? 0);
                    if ($memberId > 0) {

                        $db = db_connect();

                        $db->table('password_resets')
                            ->where('member_id', $memberId)
                            ->delete();

                        $token = bin2hex(random_bytes(32));
                        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

                        $db->table('password_resets')->insert([
                            'member_id'  => $memberId,
                            'token'      => $token,
                            'created_at' => date('Y-m-d H:i:s'),
                            'expires_at' => $expiresAt,
                        ]);

                        $base = rtrim(config('App')->baseURL, '/');
                        $link = $base . '/membership/reset/' . $token;

                        $row['body_html'] = str_replace('{{activation_link}}', $link, $row['body_html']);
                        $row['body_text'] = str_replace('{{activation_link}}', $link, $row['body_text']);
                    }
                }

                try {

                    $email->clear(true);
                    $email->setFrom($emailCfg->fromEmail, $emailCfg->fromName);

                    if ($isTestMode) {
                        $email->setTo($testAddress);
                        $email->setSubject('[TEST] ' . $row['subject']);
                        $email->setMessage($row['body_html']);
                        $email->setAltMessage($row['body_text']);
                    } else {
                        $email->setTo($toEmail, $row['to_name'] ?? null);
                        $email->setSubject($row['subject']);
                        $email->setMessage($row['body_html']);
                        $email->setAltMessage($row['body_text']);
                    }

                    if ($email->send()) {
                        $model->update($row['id'], [
                            'status' => 'sent',
                            'sent_at' => date('Y-m-d H:i:s'),
                            'last_error' => null,
                        ]);
                        $result['sent']++;
                        $consecutiveFailures = 0;
                        CLI::write("  ✓ Sent to: " . $toEmail, 'green');
                    } else {
                        throw new \RuntimeException('SMTP send failed');
                    }
                } catch (\Throwable $e) {
                    $errorMsg = substr($e->getMessage(), 0, 500);
                    $model->update($row['id'], [
                        'status' => 'failed',
                        'last_error' => $errorMsg,
                    ]);
                    $result['failed']++;
                    $consecutiveFailures++;

                    CLI::write("  ✗ Failed: " . $toEmail . " - " . substr($errorMsg, 0, 80), 'red');

                    $result['backoff'][] = [
                        'row_id' => $row['id'],
                        'email' => $toEmail,
                        'error' => substr($errorMsg, 0, 200),
                    ];

                    // Auto-pause after consecutive failures
                    if ($consecutiveFailures >= $AUTO_PAUSE_AFTER_FAIL) {
                        $pauseUntil = time() + ($AUTO_PAUSE_MINUTES * 60);
                        $cache->save($pauseKey, $pauseUntil, $AUTO_PAUSE_MINUTES * 60);
                        $result['paused'] = true;
                        CLI::write("⏸ Auto-paused for {$AUTO_PAUSE_MINUTES} minutes after {$consecutiveFailures} consecutive failures", 'red');
                        break;
                    }
                }

                $result['processed']++;

                // Delay between sends
                $delayMs = (int)($cfg->sendDelayMs ?? 0);
                if ($delayMs > 0) {
                    usleep($delayMs * 1000);
                }
            }

            // Final summary
            CLI::write('─────────────────────────────────────', 'light_gray');
            CLI::write(
                '📊 Summary: ' . $result['sent'] . ' sent, ' . $result['failed'] . ' failed',
                $result['failed'] === 0 ? 'green' : 'yellow'
            );
            CLI::write('─────────────────────────────────────', 'light_gray');

            $status = $result['failed'] === 0 ? 'success' : 'partial';
        } catch (\Throwable $e) {
            $result['errors'][] = $e->getMessage();
            $status = 'error';
            CLI::write('✗ Error: ' . $e->getMessage(), 'red');
        }

        /* ---------------------------------------------------------
         * CRON LOGGING – END
         * --------------------------------------------------------- */
        $this->writeCronLog($logModel, $jobName, $status, $result, $started);
    }

    private function writeCronLog(CronLogModel $logModel, string $jobName, string $status, array $result, string $started)
    {
        $logModel->insert([
            'job_name'    => $jobName,
            'status'      => $status,
            'summary'     => json_encode($result, JSON_PRETTY_PRINT),
            'started_at'  => $started,
            'finished_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
