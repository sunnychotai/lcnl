<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class EmailQueue extends BaseConfig
{
    public bool $sendEnabled = true;           // default; overridden by .env
    public bool $dryRun = false;          // default; overridden by .env
    public int $batchSize = 100;
    public int $dailyLimit = 500;            // safety cap

    public function __construct()
    {
        parent::__construct();
        $this->sendEnabled = env('queue.email.send', $this->sendEnabled);
        $this->dryRun = env('queue.email.dryRun', $this->dryRun);
        $this->batchSize = (int) env('queue.email.batchSize', $this->batchSize);
        $this->dailyLimit = (int) env('queue.email.dailyLimit', $this->dailyLimit);
    }
}
