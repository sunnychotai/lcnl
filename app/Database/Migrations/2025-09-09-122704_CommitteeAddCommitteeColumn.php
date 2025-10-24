<?php

// app/Database/Migrations/2025-09-09-CommitteeAddCommitteeColumn.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CommitteeAddCommitteeColumn extends Migration
{
    public function up()
    {
        $this->forge->addColumn('committee', [
            'committee' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'role', // or whichever column you want it after
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('committee', 'committee');
    }
}
