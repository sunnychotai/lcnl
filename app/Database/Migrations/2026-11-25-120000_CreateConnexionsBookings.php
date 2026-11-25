<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Connexions – Singles Mix & Mingle bookings: one row per booking (the person
 * paying), and one row per ticket holder in connexions_attendees.
 */
class CreateConnexionsBookings extends Migration
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
            // CNX26-7F3A2C. Shown to the booker and at the door; never used to
            // look a booking up from a public URL.
            'booking_ref' => [
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
            // Self-declared, for LCNL's information only; never checked.
            'is_member' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            // The booker confirmed everyone is a Gujarati single aged 25–40.
            'eligibility_confirmed' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'privacy_consent' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'girl_tickets' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'boy_tickets' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
            ],
            // Fixed when the booking is made; amount is what Stripe must charge.
            'ticket_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            // Secret for the "finish paying" link, so a booker can pay from
            // another device without anyone guessing someone else's link.
            'pay_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            // The latest Checkout Session; reused while still open so a second
            // click never opens a second payment.
            'stripe_checkout_session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'stripe_payment_intent_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            // An unpaid booking holds its places until then (the Checkout's expiry).
            'hold_until' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
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
            // Set once, so the booker and attendees are only ever emailed
            // automatically the first time; admins can resend by hand.
            'confirmed_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'pending', // pending, paid, cancelled
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
        $this->forge->addUniqueKey('pay_token');
        $this->forge->addKey('email');
        $this->forge->addKey('status');
        $this->forge->createTable('connexions_bookings');

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
            'gender' => [
                'type'       => 'VARCHAR',
                'constraint' => 5, // girl, boy
            ],
            // Girl 1, Girl 2, … within the booking.
            'position' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
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
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('booking_id');
        $this->forge->addKey('email');
        $this->forge->createTable('connexions_attendees');
    }

    public function down()
    {
        $this->forge->dropTable('connexions_attendees', true);
        $this->forge->dropTable('connexions_bookings', true);
    }
}
