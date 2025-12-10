<?php

namespace App\Commands;

use App\Models\CronLogModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CronPurge extends BaseCommand
{
    protected $group = 'System';
    protected $name = 'cron:purge';
    protected $description = 'Delete cron log entries older than 7 days.';

    public function run(array $params)
    {
        $days = 7;
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $model = new CronLogModel();

        // Count how many would be deleted
        $count = $model->where('created_at <', $cutoff)->countAllResults();

        if ($count > 0) {
            // Delete
            $model->where('created_at <', $cutoff)->delete();
            CLI::write("Purged {$count} cron log entries older than {$days} days.", 'green');
        } else {
            CLI::write("No cron logs older than {$days} days found.", 'yellow');
        }
    }
}
