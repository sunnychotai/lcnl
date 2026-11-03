<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCardPaymentToChopdaRegistrations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('chopda_registrations', [
            // 'card' = Stripe Checkout, 'bank' = transfer matched by an admin.
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'bank',
                'after'      => 'payment_timing',
            ],
            // Secret for the "pay by card" link in emails, so a registrant can
            // pay from another device without anyone guessing someone else's.
            'pay_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'payment_method',
            ],
            // The latest Checkout Session; reused while still open so a second
            // click never opens a second payment.
            'stripe_checkout_session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'pay_token',
            ],
            'stripe_payment_intent_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'stripe_checkout_session_id',
            ],
        ]);

        $this->forge->addKey('pay_token', false, true);
        $this->forge->processIndexes('chopda_registrations');
    }

    public function down()
    {
        // The pay_token index goes with its column.
        $this->forge->dropColumn('chopda_registrations', [
            'payment_method', 'pay_token', 'stripe_checkout_session_id', 'stripe_payment_intent_id',
        ]);
    }
}
