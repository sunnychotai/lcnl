<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEventIdToEventRegistrations extends Migration
{
    public function up()
    {
        // Add event_id (nullable initially)
        $this->forge->addColumn('event_registrations', [
            'event_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
                'after' => 'id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('event_registrations', 'event_id');
    }
}
