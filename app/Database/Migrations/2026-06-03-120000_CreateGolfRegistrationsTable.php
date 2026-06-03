<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGolfRegistrationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'registration_ref' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],

            // Player 1 – required
            'p1_first_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'p1_last_name'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'p1_email'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'p1_phone'      => ['type' => 'VARCHAR', 'constraint' => 30,  'null' => false],
            'p1_handicap'   => ['type' => 'DECIMAL', 'constraint' => '5,1', 'null' => false],
            'p1_meal'       => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => false],

            // Player 2 – optional
            'p2_first_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'p2_last_name'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'p2_email'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'p2_phone'      => ['type' => 'VARCHAR', 'constraint' => 30,  'null' => true, 'default' => null],
            'p2_handicap'   => ['type' => 'DECIMAL', 'constraint' => '5,1', 'null' => true, 'default' => null],
            'p2_meal'       => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => true, 'default' => null],

            // Player 3 – optional
            'p3_first_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'p3_last_name'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'p3_email'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'p3_phone'      => ['type' => 'VARCHAR', 'constraint' => 30,  'null' => true, 'default' => null],
            'p3_handicap'   => ['type' => 'DECIMAL', 'constraint' => '5,1', 'null' => true, 'default' => null],
            'p3_meal'       => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => true, 'default' => null],

            'status'       => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => false, 'default' => 'submitted'],
            'agreed_terms' => ['type' => 'TINYINT', 'constraint' => 1,   'null' => false, 'default' => 0],
            'ip_address'   => ['type' => 'VARCHAR', 'constraint' => 45,  'null' => true, 'default' => null],
            'created_at'   => ['type' => 'DATETIME', 'null' => true, 'default' => null],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true, 'default' => null],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('registration_ref');
        $this->forge->createTable('golf_registrations');
    }

    public function down()
    {
        $this->forge->dropTable('golf_registrations', true);
    }
}
