<?php

namespace App\Models;

use CodeIgniter\Model;

class CronLogModel extends Model
{
    protected $table = 'cron_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true; // populates created_at
    protected $allowedFields = [
        'job_name',
        'status',
        'summary',
        'started_at',
        'finished_at',
        'created_at',
        'updated_at'
    ];
}
