<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTicketUrlLabelToEvents extends Migration
{
    public function up()
    {
        $fields = [
            'ticket_url_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'purchase',
                'after'      => 'purchase_ticket_url',
            ],
        ];

        $this->forge->addColumn('events', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('events', 'ticket_url_label');
    }
}
