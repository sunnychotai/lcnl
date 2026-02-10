<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMembershipPaymentsAndStripeWebhookEvents extends Migration
{
    public function up()
    {
        // membership_payments
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'member_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],

            'provider' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'stripe'],
            'purpose' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'life_upgrade'], // future-proof
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'initiated'],   // initiated|paid|failed|refunded|cancelled

            'amount_minor' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'currency' => ['type' => 'CHAR', 'constraint' => 3, 'default' => 'GBP'],

            'stripe_checkout_session_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'stripe_payment_intent_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'stripe_customer_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],

            'idempotency_key' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],

            'failure_code' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'failure_message' => ['type' => 'TEXT', 'null' => true],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->addUniqueKey('stripe_checkout_session_id');
        $this->forge->addUniqueKey('idempotency_key');
        $this->forge->createTable('membership_payments', true);

        // stripe_webhook_events
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'stripe_event_id' => ['type' => 'VARCHAR', 'constraint' => 255],
            'event_type' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'api_version' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'livemode' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],

            'payload_json' => ['type' => 'LONGTEXT'],
            'signature_header' => ['type' => 'TEXT', 'null' => true],

            'received_at' => ['type' => 'DATETIME', 'null' => true],
            'processed_at' => ['type' => 'DATETIME', 'null' => true],
            'process_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true], // ok|duplicate|error|ignored
            'process_error' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('stripe_event_id');
        $this->forge->createTable('stripe_webhook_events', true);

        // optional membership audit
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'member_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'action' => ['type' => 'VARCHAR', 'constraint' => 80],
            'meta_json' => ['type' => 'LONGTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->createTable('membership_audit', true);

        // members table additions (safe to wrap in try/catch)
        $db = \Config\Database::connect();
        $fields = $db->getFieldData('members');
        $existing = array_map(fn($f) => $f->name, $fields);

        if (!in_array('life_membership_paid_at', $existing, true)) {
            $db->query("ALTER TABLE members ADD life_membership_paid_at DATETIME NULL");
        }
        if (!in_array('life_membership_payment_id', $existing, true)) {
            $db->query("ALTER TABLE members ADD life_membership_payment_id INT NULL");
        }
    }

    public function down()
    {
        $this->forge->dropTable('membership_audit', true);
        $this->forge->dropTable('stripe_webhook_events', true);
        $this->forge->dropTable('membership_payments', true);

        $db = \Config\Database::connect();
        // down-migrations for members columns are optional; skip if you prefer safety
        // $db->query("ALTER TABLE members DROP COLUMN life_membership_paid_at");
        // $db->query("ALTER TABLE members DROP COLUMN life_membership_payment_id");
    }
}
