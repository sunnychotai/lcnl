<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateFamiliesAddInviteCode extends Migration
{
    public function up()
    {
        // Add invite_code for easy family linking (shareable short code)
        $fields = [
            'invite_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 12,
                'null'       => true,
                'after'      => 'household_name',
            ],
        ];
        $this->forge->addColumn('families', $fields);

        // Unique index on invite_code
        $this->db->query('CREATE UNIQUE INDEX IF NOT EXISTS families_invite_code_uq ON families (invite_code)');
    }

    public function down()
    {
        $this->forge->dropColumn('families', 'invite_code');
    }
}
