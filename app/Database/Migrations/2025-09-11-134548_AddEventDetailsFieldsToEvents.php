<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEventDetailsFieldsToEvents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('events', [
            'ticketinfo' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'description', // move if you prefer a different spot
            ],
            'eventterms' => [
                'type' => 'MEDIUMTEXT',   // terms can be longer
                'null' => true,
                'after' => 'ticketinfo',
            ],
            'contactinfo' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'eventterms',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('events', 'ticketinfo');
        $this->forge->dropColumn('events', 'eventterms');
        $this->forge->dropColumn('events', 'contactinfo');
    }
}
