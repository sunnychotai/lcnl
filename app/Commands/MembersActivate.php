<?php

namespace App\Commands;

use App\Models\MemberModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\App;

class MembersActivate extends BaseCommand
{
    protected $group = 'LCNL';
    protected $name = 'members:activate';
    protected $description = 'Queue activation emails (password-set links) for pending members in batches.';
    protected $usage = 'php spark members:activate --batch 50 [--limit 10] [--test] [--testEmail=someone@example.com]';
    protected $options = [
        'batch' => 'Number of members to fetch in this run (default 50)',
        'limit' => 'Maximum number of members to process (default = unlimited)',
        'test' => 'Enable test mode — emails sent to testEmail, no DB updates',
        'testEmail' => 'Override the default test email address',
    ];

    public function run(array $params)
    {
        /* ---------------------------------------------------------
         * TEST MODE
         * --------------------------------------------------------- */
        $isTestMode = CLI::getOption('test') !== null;
        $testEmail = CLI::getOption('testEmail') ?: 'sunnychotai@me.com';

        if ($isTestMode) {
            CLI::write("===============================================", 'yellow');
            CLI::write(" TEST MODE ENABLED", 'yellow');
            CLI::write(" All activation emails will go to: {$testEmail}", 'yellow');
            CLI::write(" Members WILL NOT be updated.", 'yellow');
            CLI::write("===============================================", 'yellow');
        }

        /* ---------------------------------------------------------
         * BATCH SIZE
         * --------------------------------------------------------- */
        $batch = (int) (CLI::getOption('batch') ?? 50);
        if ($batch < 1 || $batch > 2000)
            $batch = 50;

        /* ---------------------------------------------------------
         * LIMIT OPTION
         * --------------------------------------------------------- */
        $limit = CLI::getOption('limit');
        $limit = is_numeric($limit) ? (int) $limit : null; // null = unlimited

        if ($limit !== null && $limit < 1) {
            CLI::write("Invalid --limit value. Must be > 0.", 'red');
            return;
        }

        if ($limit !== null) {
            CLI::write("Processing limit: $limit members (max)", 'yellow');
        }

        $model = new MemberModel();

        /* ---------------------------------------------------------
         * FETCH MEMBERS
         * --------------------------------------------------------- */
        $members = $model->where('status', 'pending')
            ->where('is_placeholder_email', 0)
            ->where('activation_sent_at', null)
            ->where('email IS NOT', null, false)
            ->where('email <>', '')
            ->orderBy('id', 'ASC')
            ->findAll($batch);

        if (!$members) {
            CLI::write('No pending members to process.', 'yellow');
            return;
        }

        /* ---------------------------------------------------------
         * IF LIMIT < batch, reduce the dataset
         * --------------------------------------------------------- */
        if ($limit !== null) {
            $members = array_slice($members, 0, $limit);
        }

        $db = db_connect();
        $passwordResets = $db->table('password_resets');

        $queued = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($members as $m) {
            try {
                $email = $m['email'];
                if (!$email) {
                    $skipped++;
                    continue;
                }

                /* ---------------------------------------------------------
                 * GENERATE TOKEN
                 * --------------------------------------------------------- */
                if (!$isTestMode) {
                    $token = bin2hex(random_bytes(32));

                    $passwordResets->insert([
                        'member_id' => $m['id'],
                        'token' => $token,
                        'created_at' => date('Y-m-d H:i:s'),
                        'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours')),
                    ]);
                } else {
                    // Fake token for test mode
                    $token = 'TEST_' . bin2hex(random_bytes(8));
                }

                /* ---------------------------------------------------------
                 * Resolve base URL
                 * --------------------------------------------------------- */
                $envBase = getenv('app.baseURL');
                $base = $envBase ? rtrim($envBase, '/') : 'https://lcnl.org';

                $link = $base . '/membership/reset/' . $token;

                /* ---------------------------------------------------------
                 * Queue email
                 * --------------------------------------------------------- */
                $emailQueue = new \App\Models\EmailQueueModel();

                $toEmail = $isTestMode ? $testEmail : $email;
                $subject = ($isTestMode ? '[TEST] ' : '') . 'Activate your LCNL Member Account';

                $bodyHtml = view('emails/membership_activation', [
                    'name' => trim($m['first_name'] . ' ' . $m['last_name']),
                    'link' => $link,
                ]);

                if ($isTestMode) {
                    $banner = "
                        <div style=\"padding:10px;background:#ffdddd;color:#a30000;
                            border:1px solid #ffaaaa;margin-bottom:15px;font-weight:bold;\">
                            TEST MODE — Originally intended for {$email}
                        </div>
                    ";
                    $bodyHtml = $banner . $bodyHtml;
                }

                $emailQueue->enqueue([
                    'to_email' => $toEmail,
                    'to_name' => $m['first_name'] . ' ' . $m['last_name'],
                    'subject' => $subject,
                    'body_html' => $bodyHtml,
                    'body_text' => strip_tags($bodyHtml),
                    'from_email' => 'info@lcnl.co.uk',
                    'from_name' => 'LCNL Membership Team',
                ]);

                /* ---------------------------------------------------------
                 * Update member ONLY if not in test mode
                 * --------------------------------------------------------- */
                if (!$isTestMode) {
                    $model->update($m['id'], [
                        'activation_sent_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $queued++;

            } catch (\Throwable $e) {
                $errors++;
                CLI::error("Error member #{$m['id']} ({$m['email']}): " . $e->getMessage());
            }
        }

        /* ---------------------------------------------------------
         * RESULTS
         * --------------------------------------------------------- */
        CLI::write("Batch Fetched: " . $batch, 'green');
        CLI::write("Processed:     " . count($members), 'green');
        CLI::write("Queued:        {$queued}", 'green');
        CLI::write("Skipped:       {$skipped}", 'yellow');
        CLI::write("Errors:        {$errors}", 'red');

        if ($isTestMode) {
            CLI::write("TEST MODE COMPLETE — No database updates performed.", 'yellow');
        }
    }
}
