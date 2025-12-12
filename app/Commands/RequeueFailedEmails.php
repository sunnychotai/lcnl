<?php

namespace App\Commands;

use App\Models\EmailQueueModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RequeueFailedEmails extends BaseCommand
{
    protected $group = 'Email';
    protected $name = 'emails:requeue-failed';
    protected $description = 'Re-queue failed emails that are safe to retry.';
    protected $usage = 'php spark emails:requeue-failed --limit 5 --max-attempts 3 --delay-minutes 30';

    protected $options = [
        'limit' => 'Maximum number of failed emails to requeue (default: unlimited)',
        'max-attempts' => 'Only requeue emails with attempts less than this (default: 3)',
        'delay-minutes' => 'Delay before retry in minutes (default: 30)',
        'dry-run' => 'Show how many emails would be requeued without updating anything',
    ];

    public function run(array $params)
    {
        $maxAttempts = (int) (CLI::getOption('max-attempts') ?? 3);
        $delayMinutes = (int) (CLI::getOption('delay-minutes') ?? 30);
        $limitOpt = CLI::getOption('limit');
        $dryRun = CLI::getOption('dry-run') !== null;

        $limit = (is_numeric($limitOpt) && (int) $limitOpt > 0)
            ? (int) $limitOpt
            : null;

        if ($maxAttempts < 1) {
            $maxAttempts = 1;
        }

        if ($delayMinutes < 1) {
            $delayMinutes = 5;
        }

        $model = new EmailQueueModel();

        $scheduledAt = date('Y-m-d H:i:s', time() + ($delayMinutes * 60));

        /*
         * STEP 1: Select eligible failed emails (IDs only)
         */
        $query = $model->where('status', 'failed')
            ->where('attempts <', $maxAttempts)
            ->orderBy('updated_at', 'ASC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        $rows = $query->findAll();
        $count = count($rows);

        if ($count === 0) {
            CLI::write('No failed emails eligible for requeue.', 'yellow');
            return;
        }

        CLI::write("Eligible failed emails: {$count}", 'green');
        CLI::write("Max attempts: {$maxAttempts}", 'green');
        CLI::write("Retry scheduled in: {$delayMinutes} minutes ({$scheduledAt})", 'green');

        if ($dryRun) {
            CLI::write('DRY RUN — no updates performed.', 'yellow');
            return;
        }

        /*
         * STEP 2: Requeue selected IDs only
         */
        $ids = array_column($rows, 'id');

        $model->whereIn('id', $ids)
            ->set('status', 'pending')
            ->set('scheduled_at', $scheduledAt)
            ->set(
                'last_error',
                "CONCAT('[REQUEUED " . date('Y-m-d H:i:s') . "] ', IFNULL(last_error, ''))",
                false
            )
            ->update();

        CLI::write("Requeued {$count} failed emails.", 'green');
    }
}
