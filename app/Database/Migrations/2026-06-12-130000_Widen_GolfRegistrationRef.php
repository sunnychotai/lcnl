<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WidenGolfRegistrationRef extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('golf_registrations', [
            'registration_ref' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => false],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('golf_registrations', [
            'registration_ref' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
        ]);
    }
}
