<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeTeamNameNotNull extends Migration
{
    public function up()
    {
        // Ensure no NULLs exist before adding the NOT NULL constraint
        $this->db->query("UPDATE golf_registrations SET team_name = '' WHERE team_name IS NULL");

        $this->forge->modifyColumn('golf_registrations', [
            'team_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'default'    => '',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('golf_registrations', [
            'team_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
        ]);
    }
}
