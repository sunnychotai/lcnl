<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PasswordResets extends Migration
{
    public function up()
    {
        $this->forge->addField([
    'id'         => ['type' => 'INT','auto_increment' => true],
    'member_id'  => ['type' => 'INT'],
    'token'      => ['type' => 'VARCHAR','constraint' => 128],
    'created_at' => ['type' => 'DATETIME'],
    'expires_at' => ['type' => 'DATETIME'],
    'used_at'    => ['type' => 'DATETIME','null' => true],
]);
$this->forge->addKey('id', true);
$this->forge->createTable('password_resets');

    }

    public function down()
    {
        $this->forge->dropTable('password_resets');
    }
}
