<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeamNameToGolfRegistrations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('golf_registrations', [
            'team_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'registration_ref',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('golf_registrations', 'team_name');
    }
}
