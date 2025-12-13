<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEventRegistrationsTable extends Migration
{
    public function up()
    {
        // -----------------------------
        // Add member_id if missing
        // -----------------------------
        if (! $this->db->fieldExists('member_id', 'event_registrations')) {
            $this->forge->addColumn('event_registrations', [
                'member_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        // -----------------------------
        // Fix status column
        // -----------------------------
        // Use raw SQL for ENUM safety
        $this->db->query("
            ALTER TABLE event_registrations
            MODIFY status
            ENUM('submitted','confirmed','cancelled')
            NOT NULL
            DEFAULT 'submitted'
        ");
    }

    public function down()
    {
        // -----------------------------
        // Rollback status enum
        // -----------------------------
        $this->db->query("
            ALTER TABLE event_registrations
            MODIFY status
            ENUM('pending','confirmed','cancelled')
            NOT NULL
            DEFAULT 'pending'
        ");

        // -----------------------------
        // Drop member_id if exists
        // -----------------------------
        if ($this->db->fieldExists('member_id', 'event_registrations')) {
            $this->forge->dropColumn('event_registrations', 'member_id');
        }
    }
}
