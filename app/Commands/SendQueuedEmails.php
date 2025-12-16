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
        CLI::write('env(app.baseURL)=' . (env('app.baseURL') ?? 'NULL'), 'yellow');
        CLI::write('config(App)->baseURL=' . config('App')->baseURL, 'yellow');

        /* ---------------------------------------------------------
         * CONFIG / CONSTANTS (Fasthosts-safe)
         * --------------------------------------------------------- */
        $HARD_PER_RUN_CAP = 5;
        $TEN_MIN_CAP = 50;
        $DAILY_CAP = 950;
        $AUTO_PAUSE_AFTER_FAIL = 5;
        $AUTO_PAUSE_MINUTES = 10;

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
                CLI::write("⏸ Email sending paused.", 'yellow');
                $result['paused'] = true;
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            $cfg = config('EmailQueue');
            $emailCfg = config('Email');
            $batch = max(1, (int) ($cfg->batchSize ?? 50));

            $model = new EmailQueueModel();

            /* ---------------------------------------------------------
             * ROLLING RATE LIMITS (CRITICAL)
             * --------------------------------------------------------- */
            $now = time();
            $tenMinAgo = date('Y-m-d H:i:s', $now - 600);
            $dayAgo = date('Y-m-d H:i:s', $now - 86400);

            $sent24h = $model->where('status', 'sent')
                ->where('sent_at >=', $dayAgo)
                ->countAllResults();
            $model->resetQuery();

            $sent10m = $model->where('status', 'sent')
                ->where('sent_at >=', $tenMinAgo)
                ->countAllResults();
            $model->resetQuery();

            $remaining24h = max(0, $DAILY_CAP - $sent24h);
            $remaining10m = max(0, $TEN_MIN_CAP - $sent10m);

            $take = min(
                $batch,
                $HARD_PER_RUN_CAP,
                $remaining24h,
                $remaining10m
            );

            if ($take <= 0) {
                CLI::write(
                    $remaining24h <= 0
                    ? '✗ Daily email limit reached'
                    : ($remaining10m <= 0
                        ? '✗ 10-minute rate limit reached'
                        : '✗ Per-run cap reached'),
                    'yellow'
                );

                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            /* ---------------------------------------------------------
             * FETCH PENDING EMAILS (LIMITED)
             * --------------------------------------------------------- */
            $rows = $model
                ->where('status', 'pending')
                ->groupStart()
                ->where('scheduled_at', null)
                ->orWhere('scheduled_at <=', date('Y-m-d H:i:s'))
                ->groupEnd()
                ->orderBy('priority', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll($take);

            if (!$rows) {
                CLI::write("✓ No emails to send.", 'green');
                $this->writeCronLog($logModel, $jobName, 'success', $result, $started);
                return;
            }

            CLI::write("📧 Processing " . count($rows) . " email(s)...", 'green');

            $email = service('email');
            $memberModel = new MemberModel();

            foreach ($rows as $row) {

                $toEmail = trim((string) ($row['to_email'] ?? ''));

                if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                    $model->update($row['id'], [
                        'status' => 'failed',
                        'last_error' => 'INVALID_EMAIL_FORMAT',
                    ]);
                    CLI::write("  ✗ Invalid format: {$toEmail}", 'red');
                    $result['failed']++;
                    continue;
                }

                $member = null;
                if (!empty($row['related_id'])) {
                    $member = $memberModel->find((int) $row['related_id']);
                }

                if ($member && (int) ($member['is_valid_email'] ?? 0) !== 1) {
                    $model->update($row['id'], [
                        'status' => 'failed',
                        'last_error' => 'EMAIL_MARKED_INVALID',
                    ]);
                    CLI::write("  ✗ Skipped (invalid): {$toEmail}", 'yellow');
                    $result['failed']++;
                    continue;
                }

                $model->update($row['id'], [
                    'status' => 'sending',
                    'attempts' => (int) ($row['attempts'] ?? 0) + 1,
                ]);

                $placeholders = ['{{current_year}}' => date('Y')];

                if ($member) {
                    $name = trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
                    if ($name !== '') {
                        $placeholders['{{member_name}}'] = $name;
                    }
                }

                if (($row['type'] ?? null) === 'member_activation' && $member) {
                    $db = db_connect();

                    $db->table('password_resets')
                        ->where('member_id', (int) $member['id'])
                        ->delete();

                    $token = bin2hex(random_bytes(32));

                    $db->table('password_resets')->insert([
                        'member_id' => (int) $member['id'],
                        'token' => $token,
                        'created_at' => date('Y-m-d H:i:s'),
                        'expires_at' => date('Y-m-d H:i:s', strtotime('+72 hours')),
                    ]);

                    $base = rtrim(env('app.baseURL'), '/');
                    $placeholders['{{activation_link}}'] = $base . '/membership/reset/' . $token;
                }

                $rawHtml = str_replace(
                    array_keys($placeholders),
                    array_values($placeholders),
                    (string) ($row['body_html'] ?? '')
                );

                $rawText = (string) ($row['body_text'] ?? '');
                if ($rawText === '') {
                    $rawText = trim(html_entity_decode(strip_tags($rawHtml)));
                }

                try {
                    $email->clear(true);
                    $email->setFrom($emailCfg->fromEmail, $emailCfg->fromName);

                    if ($isTestMode) {
                        $email->setTo($testAddress);
                        $email->setSubject('[TEST] ' . ($row['subject'] ?? '(no subject)'));
                    } else {
                        $email->setTo($toEmail, $row['to_name'] ?? null);
                        $email->setSubject($row['subject'] ?? '(no subject)');
                    }

                    $email->setMessage($rawHtml);
                    $email->setAltMessage($rawText);

                    if ($email->send()) {
                        $model->update($row['id'], [
                            'status' => 'sent',
                            'sent_at' => date('Y-m-d H:i:s'),
                            'last_error' => null,
                        ]);
                        CLI::write("  ✓ Sent to: {$toEmail}", 'green');
                        $result['sent']++;
                    } else {
                        throw new \RuntimeException('SMTP send failed');
                    }
                } catch (\Throwable $e) {
                    $model->update($row['id'], [
                        'status' => 'failed',
                        'last_error' => substr($e->getMessage(), 0, 500),
                    ]);
                    CLI::write("  ✗ Failed: {$toEmail}", 'red');
                    $result['failed']++;
                }

                $result['processed']++;

                if (($cfg->sendDelayMs ?? 0) > 0) {
                    usleep((int) $cfg->sendDelayMs * 1000);
                }
            }

            $status = $result['failed'] === 0 ? 'success' : 'partial';

        } catch (\Throwable $e) {
            $result['errors'][] = $e->getMessage();
            $status = 'error';
            CLI::write('✗ Error: ' . $e->getMessage(), 'red');
        }

        $this->writeCronLog($logModel, $jobName, $status, $result, $started);
    }

    private function writeCronLog(
        CronLogModel $logModel,
        string $jobName,
        string $status,
        array $result,
        string $started
    ) {
        $logModel->insert([
            'job_name' => $jobName,
            'status' => $status,
            'summary' => json_encode($result, JSON_PRETTY_PRINT),
            'started_at' => $started,
            'finished_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
