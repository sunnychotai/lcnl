<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropEventNameFromEventRegistrations extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('event_registrations', 'event_name');
    }

    public function down()
    {
        $this->forge->addColumn('event_registrations', [
            'event_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
                'after'      => 'ip_address',
            ],
        ]);
    }
}
