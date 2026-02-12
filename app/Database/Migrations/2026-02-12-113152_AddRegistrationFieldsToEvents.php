<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegistrationFieldsToEvents extends Migration
{
    public function up()
    {
        $fields = [
            'requires_registration' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'contactinfo', // adjust if contactinfo doesn't exist in your schema
            ],
            'capacity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,          // NULL = unlimited
                'after' => 'requires_registration',
            ],
            'registration_open' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false,
                'after' => 'capacity',
            ],
        ];

        $this->forge->addColumn('events', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('events', 'requires_registration');
        $this->forge->dropColumn('events', 'capacity');
        $this->forge->dropColumn('events', 'registration_open');
    }
}
