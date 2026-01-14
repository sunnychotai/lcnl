<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMemberDisableReason extends Migration
{
    public function up()
    {
        $this->forge->addColumn('members', [
            'disabled_reason' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'status',
            ],
            'disabled_notes' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'disabled_reason',
            ],
            'disabled_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'disabled_notes',
            ],
            'disabled_by' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'disabled_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('members', [
            'disabled_reason',
            'disabled_notes',
            'disabled_at',
            'disabled_by',
        ]);
    }
}
