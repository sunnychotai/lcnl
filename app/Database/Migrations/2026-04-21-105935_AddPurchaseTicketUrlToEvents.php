<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPurchaseTicketUrlToEvents extends Migration
{
    public function up()
    {
        $fields = [
            'purchase_ticket_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'ticketinfo',
            ],
        ];

        $this->forge->addColumn('events', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('events', 'purchase_ticket_url');
    }
}
