<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMemberFamily extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'member_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'name' => ['type' => 'varchar', 'constraint' => 120],
            'relation' => ['type' => 'varchar', 'constraint' => 40], // spouse, child, parent, other
            'year_of_birth' => ['type' => 'SMALLINT', 'null' => true],
            'gender' => ['type' => 'varchar', 'constraint' => 20, 'null' => true],
            'notes' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_family');
    }

    public function down()
    {
        $this->forge->dropTable('member_family');
    }
}
