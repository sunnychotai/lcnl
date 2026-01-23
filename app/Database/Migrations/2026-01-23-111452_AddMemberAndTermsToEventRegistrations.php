<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMemberAndTermsToEventRegistrations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('event_registrations', [
            'is_lcnl_member' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'member_id',
            ],
            'agreed_terms' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'notes',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('event_registrations', ['is_lcnl_member', 'agreed_terms']);
    }
}
