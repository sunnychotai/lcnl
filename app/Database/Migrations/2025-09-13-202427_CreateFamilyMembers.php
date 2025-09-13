<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFamilyMembers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'family_id'  => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'member_id'  => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'role'       => ['type'=>'ENUM','constraint'=>['lead','spouse','dependent','other'],'default'=>'other'],
            'label'      => ['type'=>'VARCHAR','constraint'=>50,'null'=>true], // e.g., "Grandparent"
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['family_id','member_id']);
        $this->forge->addForeignKey('family_id', 'families', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('family_members', true);
    }

    public function down()
    {
        $this->forge->dropTable('family_members', true);
    }
}
