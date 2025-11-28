<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExtendMembers extends Migration
{
    public function up()
    {
        $fields = [
            'date_of_birth' => ['type' => 'date', 'null' => true, 'after' => 'last_name'],
            'gender' => ['type' => 'varchar', 'constraint' => 20, 'null' => true, 'after' => 'date_of_birth'],
            // keep consent_at you already have; we standardize normalisation with a trigger at app level
        ];
        $this->forge->addColumn('members', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('members', ['date_of_birth', 'gender']);
    }
}
