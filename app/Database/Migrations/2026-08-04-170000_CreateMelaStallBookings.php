<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMelaStallBookings extends Migration
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
            'booking_ref' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
            ],
            // Free text shown when category is "other"
            'category_other' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'is_food_stall' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'items_description' => [
                'type' => 'TEXT',
            ],
            'contact_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'contact_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'contact_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'vehicle_reg' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'comments' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // What the stall holder ticked
            'confirmed_payment' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'agreed_terms' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            // What LCNL verified against the bank statement — deliberately
            // separate from the stall holder's own tick box.
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
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['submitted', 'confirmed', 'cancelled'],
                'default'    => 'submitted',
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
        $this->forge->addUniqueKey('booking_ref');
        $this->forge->addKey('contact_email');
        $this->forge->addKey('status');
        $this->forge->createTable('mela_stall_bookings');

        // Supporting documents (food hygiene certificates and similar).
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'booking_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            // Name as uploaded, shown to admins
            'original_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            // Randomised name on disk, under WRITEPATH (not web-reachable)
            'stored_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'size_bytes' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('booking_id');
        $this->forge->createTable('mela_stall_documents');
    }

    public function down()
    {
        $this->forge->dropTable('mela_stall_documents', true);
        $this->forge->dropTable('mela_stall_bookings', true);
    }
}
