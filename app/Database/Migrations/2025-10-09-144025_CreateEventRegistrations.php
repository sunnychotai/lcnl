<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventRegistrations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'event_name'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'first_name'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'           => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'           => ['type' => 'VARCHAR', 'constraint' => 30],
            'num_participants'=> ['type' => 'INT', 'constraint' => 3, 'default' => 1],
            'num_guests'      => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['pending','confirmed','cancelled'], 'default' => 'pending'],
'created_at' => [
    'type'    => 'DATETIME',
    'null'    => false,
    'default' => null
],
'updated_at' => [
    'type'    => 'DATETIME',
    'null'    => true,
],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('event_registrations');
    }

    public function down()
    {
        $this->forge->dropTable('event_registrations');
    }
}
