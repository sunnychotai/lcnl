<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToCronLogs extends Migration
{
    public function up()
    {
        $fields = [
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
            ],
        ];

        $this->forge->addColumn('cron_logs', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('cron_logs', 'updated_at');
    }
}
