<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTelephoneToMemberFamily extends Migration
{
    public function up()
    {
        $fields = [
            'telephone' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'email', // or whichever column exists
            ],
        ];

        $this->forge->addColumn('member_family', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('member_family', 'telephone');
    }
}
