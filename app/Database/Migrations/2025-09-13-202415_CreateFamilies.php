<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFamilies extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'household_name'=> ['type'=>'VARCHAR','constraint'=>150,'null'=>true], // e.g., "Chotai Household"
            'lead_member_id'=> ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true],
            'address1'      => ['type'=>'VARCHAR','constraint'=>150,'null'=>true],
            'address2'      => ['type'=>'VARCHAR','constraint'=>150,'null'=>true],
            'city'          => ['type'=>'VARCHAR','constraint'=>80,'null'=>true],
            'postcode'      => ['type'=>'VARCHAR','constraint'=>12,'null'=>true],
            'created_at'    => ['type'=>'DATETIME','null'=>true],
            'updated_at'    => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lead_member_id', 'members', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('families', true);
    }

    public function down()
    {
        $this->forge->dropTable('families', true);
    }
}
