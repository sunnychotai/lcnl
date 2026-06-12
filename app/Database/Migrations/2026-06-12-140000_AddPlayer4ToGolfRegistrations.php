<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlayer4ToGolfRegistrations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('golf_registrations', [
            'p4_first_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null, 'after' => 'p3_tshirt'],
            'p4_last_name'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null, 'after' => 'p4_first_name'],
            'p4_email'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null, 'after' => 'p4_last_name'],
            'p4_phone'      => ['type' => 'VARCHAR', 'constraint' => 30,  'null' => true, 'default' => null, 'after' => 'p4_email'],
            'p4_handicap'   => ['type' => 'DECIMAL', 'constraint' => '5,1', 'null' => true, 'default' => null, 'after' => 'p4_phone'],
            'p4_meal'       => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => true, 'default' => null, 'after' => 'p4_handicap'],
            'p4_tshirt'     => ['type' => 'VARCHAR', 'constraint' => 5,   'null' => true, 'default' => null, 'after' => 'p4_meal'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('golf_registrations', [
            'p4_first_name', 'p4_last_name', 'p4_email', 'p4_phone', 'p4_handicap', 'p4_meal', 'p4_tshirt',
        ]);
    }
}
