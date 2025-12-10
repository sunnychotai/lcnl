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
    protected $usage = 'php spark members:activate --batch 50';
    protected $options = [
        'batch' => 'Number of members to process in this run (default 50)',
    ];

    public function run(array $params)
    {
        $batch = (int) ($params['batch'] ?? CLI::getOption('batch') ?? 50);
        if ($batch < 1 || $batch > 1000)
            $batch = 50;

        $model = new MemberModel();

        // Fetch the next batch to process
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

                // Generate token (store raw token; your reset controller will verify+expire by created_at)
                $token = bin2hex(random_bytes(32));

                $passwordResets->insert([
                    'member_id' => $m['id'],
                    'token' => $token,
                    'created_at' => date('Y-m-d H:i:s'),
                    'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours')),
                ]);


                // Build activation link
                $link = base_url('membership/reset/' . $token);

                // Queue email (adapt to your queue if needed)
                $subject = 'Activate your LCNL Member Account';
                $view = 'emails/membership_activation'; // see template below
                $data = [
                    'name' => trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? '')),
                    'link' => $link,
                ];

                // Example: Your Mailer/Queue. Replace with your app’s queue call.
                // e.g. service('mailer')->queueTemplate($email, $subject, $view, $data);
                // Queue activation email (correct LCNL method)
                $emailQueue = new \App\Models\EmailQueueModel();

                $emailQueue->enqueue([
                    'to_email' => $email,
                    'to_name' => $m['first_name'] . ' ' . $m['last_name'],
                    'subject' => 'Activate your LCNL Member Account',
                    'body_html' => view('emails/membership_activation', [
                        'name' => $m['first_name'] . ' ' . $m['last_name'],
                        'link' => $link,
                    ]),
                    'body_text' => strip_tags(
                        view('emails/membership_activation', [
                            'name' => $m['first_name'] . ' ' . $m['last_name'],
                            'link' => $link,
                        ])
                    ),
                    'from_email' => 'info@lcnl.org',
                    'from_name' => 'LCNL Membership Team',
                ]);


                // Mark sent
                $model->update($m['id'], ['activation_sent_at' => date('Y-m-d H:i:s')]);
                $queued++;

            } catch (\Throwable $e) {
                $errors++;
                CLI::error("Error member #{$m['id']} ({$m['email']}): " . $e->getMessage());
            }
        }

        CLI::write("Processed: {$batch}", 'green');
        CLI::write("Queued:    {$queued}", 'green');
        CLI::write("Skipped:   {$skipped}", 'yellow');
        CLI::write("Errors:    {$errors}", 'red');
    }
}
