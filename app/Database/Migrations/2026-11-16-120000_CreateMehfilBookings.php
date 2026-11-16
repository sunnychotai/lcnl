<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMehfilBookings extends Migration
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
            // XMAS-RONPAW, with a number added on a clash. Doubles as the bank
            // transfer reference, so it stays within the 18-character limit.
            'booking_ref' => [
                'type'       => 'VARCHAR',
                'constraint' => 18,
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
            'postcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            // Self-declared, for LCNL's information only; never checked.
            'is_member' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'individual_tickets' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'tables' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
            ],
            // Prices fixed when the booking was made, so an early bird booking
            // paid after the cut-off keeps its price.
            'price_band' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'individual_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'table_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'privacy_consent' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            // What LCNL verified against the bank statement.
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
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'booked', // booked, paid, cancelled
            ],
            'cancelled_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('email');
        $this->forge->addKey('status');
        $this->forge->createTable('mehfil_bookings');
    }

    public function down()
    {
        $this->forge->dropTable('mehfil_bookings', true);
    }
}
