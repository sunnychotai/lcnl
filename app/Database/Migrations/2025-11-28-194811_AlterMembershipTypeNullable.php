<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterMembershipTypeNullable extends Migration
{
    public function up()
    {
        $fields = [
            'membership_type' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => true,
                'default' => null,
            ],
        ];

        $this->forge->modifyColumn('memberships', $fields);
    }

    public function down()
    {
        $fields = [
            'membership_type' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => false,
            ],
        ];

        $this->forge->modifyColumn('memberships', $fields);
    }
}
