<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FinaliseEventId extends Migration
{
    public function up()
    {
        // Make NOT NULL
        $this->forge->modifyColumn('event_registrations', [
            'event_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);

        // Add foreign key
        $this->db->query("
            ALTER TABLE event_registrations
            ADD CONSTRAINT fk_event_registration
            FOREIGN KEY (event_id)
            REFERENCES events(id)
            ON DELETE RESTRICT
            ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE event_registrations
            DROP FOREIGN KEY fk_event_registration
        ");
    }
}
