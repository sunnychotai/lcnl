<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMembershipHistory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'member_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'changed_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true, // admin ID
            ],
            'old_type' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => true,
            ],
            'new_type' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
            ],
            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('membership_history');
    }

    public function down()
    {
        $this->forge->dropTable('membership_history');
    }
}
