<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddressFieldsToMembers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('members', [
            'address1' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'mobile'
            ],
            'address2' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'address1'
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'address2'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('members', ['address1', 'address2', 'city']);
    }
}
