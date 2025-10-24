<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMembers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'first_name'      => ['type'=>'VARCHAR','constraint'=>100],
            'last_name'       => ['type'=>'VARCHAR','constraint'=>100],
            'email'           => ['type'=>'VARCHAR','constraint'=>191,'null'=>true], // unique, CI collation is case-insensitive
            'mobile'          => ['type'=>'VARCHAR','constraint'=>20,'null'=>true],   // E.164 e.g. +447...
            'password_hash'   => ['type'=>'VARCHAR','constraint'=>255],
            'postcode'        => ['type'=>'VARCHAR','constraint'=>12,'null'=>true],
            'status'          => ['type'=>'ENUM','constraint'=>['pending','active','disabled'],'default'=>'pending'],
            'verified_at'     => ['type'=>'DATETIME','null'=>true],
            'verified_by'     => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true], // admin who activated
            'consent_at'      => ['type'=>'DATETIME','null'=>true],
            'last_login'      => ['type'=>'DATETIME','null'=>true],
            'source'          => ['type'=>'VARCHAR','constraint'=>50,'null'=>true], // e.g. QR campaign/event code
            'created_at'      => ['type'=>'DATETIME','null'=>true],
            'updated_at'      => ['type'=>'DATETIME','null'=>true],
            'deleted_at'      => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('mobile'); // allows multiple NULLs (children without mobile/email)
        $this->forge->createTable('members', true);
    }

    public function down()
    {
        $this->forge->dropTable('members', true);
    }
}
