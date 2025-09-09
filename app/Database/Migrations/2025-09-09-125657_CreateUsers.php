<?php
namespace App\Database\Migrations;


use CodeIgniter\Database\Migration;


class CreateUsers extends Migration
{
public function up()
{
$this->forge->addField([
'id' => [
'type' => 'INT',
'constraint' => 11,
'unsigned' => true,
'auto_increment' => true,
],
'name' => [ 'type' => 'VARCHAR', 'constraint' => 120, 'null' => false ],
'email' => [ 'type' => 'VARCHAR', 'constraint' => 190, 'null' => false ],
'password_hash' => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => false ],
'role' => [ 'type' => 'ENUM("ADMIN","WEBSITE","EVENT")', 'default' => 'WEBSITE' ],
'active' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 1 ],
'last_login_at' => [ 'type' => 'DATETIME', 'null' => true ],
'force_password_change' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 0 ],
'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
'deleted_at' => [ 'type' => 'DATETIME', 'null' => true ],
'reset_token' => [ 'type' => 'VARCHAR', 'constraint' => 100, 'null' => true ],
'reset_expires_at' => [ 'type' => 'DATETIME', 'null' => true ],
]);


$this->forge->addKey('id', true);
$this->forge->addUniqueKey('email');
$this->forge->createTable('users');
}


public function down()
{
$this->forge->dropTable('users');
}
}