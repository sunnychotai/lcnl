<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailVerifications extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'member_id'   => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'token'       => ['type'=>'VARCHAR','constraint'=>64],
            'expires_at'  => ['type'=>'DATETIME'],
            'consumed_at' => ['type'=>'DATETIME','null'=>true],
            'created_at'  => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('token');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('email_verifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('email_verifications', true);
    }
}
