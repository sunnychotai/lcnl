<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMemberAuditLog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => false,
            ],
            'type' => [ // logical category e.g. 'email', 'status', 'profile'
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'field_name' => [ // database field that changed
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'old_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'new_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'description' => [ // human-readable: “Email validity changed from VALID to INVALID”
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'changed_by' => [ // admin user id (if available)
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => 0,
            ],
            'changed_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->addKey('type');
        $this->forge->addKey('changed_by');

        // Base table
        $this->forge->createTable('member_audit_log', true);

        // (Optional) Foreign key to members.id — safe if your DB enforces FKs
        // Comment out if your environment doesn’t support FKs.
        $this->db->query(
            'ALTER TABLE member_audit_log
             ADD CONSTRAINT fk_member_audit_member
             FOREIGN KEY (member_id) REFERENCES members(id)
             ON DELETE CASCADE'
        );
    }

    public function down()
    {
        // Drop FK if it exists (ignore errors if not present)
        try {
            $this->db->query('ALTER TABLE member_audit_log DROP FOREIGN KEY fk_member_audit_member');
        } catch (\Throwable $e) {
        }

        $this->forge->dropTable('member_audit_log', true);
    }
}
