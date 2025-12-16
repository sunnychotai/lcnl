<?php

namespace App\Commands;

use App\Models\MemberModel;
use App\Models\EmailQueueModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MembersActivate extends BaseCommand
{
    protected $group = 'LCNL';
    protected $name = 'members:activate';
    protected $description = 'Queue activation emails for pending members (token generated at send time).';
    protected $usage = 'php spark members:activate --batch 50';

    protected $options = [
        'batch' => 'Number of members to queue (default 50)',
        'dry-run' => 'Preview without writing to DB',
    ];

    public function run(array $params)
    {
        $batch = (int) (CLI::getOption('batch') ?? 50);
        $dryRun = CLI::getOption('dry-run') !== null;

        if ($batch < 1 || $batch > 1000) {
            $batch = 50;
        }

        CLI::write('LCNL Member Activation Queue', 'yellow');
        CLI::write('Batch: ' . $batch);
        CLI::write('Dry run: ' . ($dryRun ? 'YES' : 'NO'));
        CLI::newLine();

        $memberModel = new MemberModel();

        $members = $memberModel
            ->where('status', 'pending')
            ->where('is_valid_email', 1)
            ->where('activation_sent_at', null)
            ->orderBy('created_at', 'ASC')
            ->findAll($batch);

        if (!$members) {
            CLI::write('No eligible members found.', 'green');
            return;
        }

        $emailQueue = new EmailQueueModel();
        $queued = 0;

        foreach ($members as $member) {

            $email = trim((string) ($member['email'] ?? ''));

            // Absolute safety check (DB already filtered, but never trust data)
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                CLI::write("Skipping invalid email for member ID {$member['id']}", 'red');
                continue;
            }

            if ($dryRun) {
                CLI::write("[DRY] Would queue: {$email}");
                $queued++;
                continue;
            }

            // Load templates
            $htmlTemplate = file_get_contents(APPPATH . 'Views/emails/membership_activation.php');
            $textTemplate = file_exists(APPPATH . 'Views/emails/membership_activation.txt')
                ? file_get_contents(APPPATH . 'Views/emails/membership_activation.txt')
                : null;

            $emailQueue->insert([
                'type' => 'member_activation',
                'related_id' => (int) $member['id'],
                'to_email' => $email,
                'to_name' => trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')),
                'subject' => 'Activate your LCNL membership',
                'body_html' => $htmlTemplate,
                'body_text' => $textTemplate,
                'priority' => 5,
                'status' => 'pending',
                'scheduled_at' => date('Y-m-d H:i:s'),
            ]);

            // Mark activation email as queued (prevents duplicates)
            $memberModel->update($member['id'], [
                'activation_sent_at' => date('Y-m-d H:i:s'),
            ]);

            CLI::write("Queued: {$email}", 'green');
            $queued++;
        }

        CLI::newLine();
        CLI::write("Total queued: {$queued}", 'yellow');
    }
}
