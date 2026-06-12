<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTshirtToGolfRegistrations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('golf_registrations', [
            'p1_tshirt' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true, 'default' => null, 'after' => 'p1_meal'],
            'p2_tshirt' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true, 'default' => null, 'after' => 'p2_meal'],
            'p3_tshirt' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true, 'default' => null, 'after' => 'p3_meal'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('golf_registrations', ['p1_tshirt', 'p2_tshirt', 'p3_tshirt']);
    }
}
