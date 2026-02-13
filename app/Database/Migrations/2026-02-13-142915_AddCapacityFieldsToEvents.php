<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCapacityFieldsToEvents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('events', [
            'max_registrations' => [
                'type' => 'INT',
                'null' => true,
            ],
            'max_headcount' => [
                'type' => 'INT',
                'null' => true,
            ],
        ]);
    }


    public function down()
    {
        //
    }
}
