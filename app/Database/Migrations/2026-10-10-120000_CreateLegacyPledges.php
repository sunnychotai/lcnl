<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLegacyPledges extends Migration
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
            'pledge_ref' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            // Title, names and address are kept in the shape HMRC's Gift Aid
            // schedule asks for, so a claim can be exported without re-keying.
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
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
            'house_name_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'street' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'town' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'postcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            // What goes on the plaque. Admins may tidy it before engraving, so
            // the donor's own wording is kept alongside.
            'plaque_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'plaque_name_submitted' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'gift_aid' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            // Evidence of the declaration: exact text, its version and when.
            'gift_aid_declared_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'gift_aid_version' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'gift_aid_wording' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'payment_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 18,
            ],
            // 'paid' = donor says they have already transferred,
            // 'will_pay' = they intend to within 7 days.
            'payment_timing' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '500.00',
            ],
            'confirmed_donation' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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
            // What LCNL verified against the LCF bank statement — separate from
            // anything the donor ticked.
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
            // Set once, so toggling paid/unpaid never sends a second thank-you.
            'thank_you_sent_at' => [
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
                'constraint' => ['pledged', 'paid', 'cancelled'],
                'default'    => 'pledged',
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
        $this->forge->addUniqueKey('pledge_ref');
        $this->forge->addKey('email');
        $this->forge->addKey('status');
        $this->forge->createTable('legacy_pledges');
    }

    public function down()
    {
        $this->forge->dropTable('legacy_pledges', true);
    }
}
