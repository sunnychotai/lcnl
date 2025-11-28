<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMemberships extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'member_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'membership_type' => ['type' => 'varchar', 'constraint' => 40], // life, annual, senior, youth, associate
            'membership_number' => ['type' => 'varchar', 'constraint' => 40, 'null' => true],
            'start_date' => ['type' => 'date', 'null' => true],
            'end_date' => ['type' => 'date', 'null' => true],
            'status' => ['type' => 'enum("pending","active","expired","cancelled")', 'default' => 'pending'],
            'notes' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('memberships');
    }

    public function down()
    {
        $this->forge->dropTable('memberships');
    }
}
