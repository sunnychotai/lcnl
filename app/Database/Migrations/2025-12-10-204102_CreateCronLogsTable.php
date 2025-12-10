<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCronLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'job_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20, // success | error | partial
            ],
            'summary' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'finished_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('job_name');
        $this->forge->addKey('status');
        $this->forge->createTable('cron_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('cron_logs', true);
    }
}
