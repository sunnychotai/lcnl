<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChopdaRegistrations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'registration_ref' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            // Everyone coming on this registration, Yajmans included, so the
            // kitchen can plan breakfast. Only two of them sit for the pooja.
            'num_attendees' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'payment_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 18,
            ],
            // 'paid' = says they have already transferred, 'will_pay' = not yet.
            'payment_timing' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '35.00',
            ],
            'privacy_consent' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'comments' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // What LCNL verified against the bank statement — separate from
            // anything the registrant ticked.
            'payment_received' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'payment_received_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'payment_marked_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            // Set once, so toggling paid/unpaid never sends a second confirmation.
            'confirmed_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reminder_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reminder_count' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['registered', 'paid', 'cancelled'],
                'default'    => 'registered',
            ],
            'admin_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('registration_ref');
        $this->forge->addKey('email');
        $this->forge->addKey('status');
        $this->forge->createTable('chopda_registrations');
    }

    public function down()
    {
        $this->forge->dropTable('chopda_registrations', true);
    }
}
