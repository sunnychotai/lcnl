<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsSoldOutToEvents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('events', [
            'is_sold_out' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'is_valid',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('events', 'is_sold_out');
    }
}
