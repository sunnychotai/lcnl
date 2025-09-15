<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMemberVerifications extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'member_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'token'      => ['type' => 'VARCHAR', 'constraint' => 64],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'expires_at' => ['type' => 'DATETIME', 'null' => false],
            'used_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->createTable('member_verifications');
    }

    public function down()
    {
        $this->forge->dropTable('member_verifications');
    }
}
