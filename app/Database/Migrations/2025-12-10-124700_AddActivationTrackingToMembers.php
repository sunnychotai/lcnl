<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActivationTrackingToMembers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('members', [
            'activation_sent_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'verified_at'],
            'activated_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'activation_sent_at'],
            'is_placeholder_email' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'null' => false, 'after' => 'email'],
        ]);

        // Helpful indexes
        $this->db->query('CREATE INDEX members_status_idx ON members (status)');
        $this->db->query('CREATE INDEX members_placeholder_idx ON members (is_placeholder_email)');
    }

    public function down()
    {
        $this->forge->dropColumn('members', ['activation_sent_at', 'activated_at', 'is_placeholder_email']);
    }
}
