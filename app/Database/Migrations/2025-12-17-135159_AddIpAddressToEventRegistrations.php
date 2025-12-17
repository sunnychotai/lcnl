<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIpAddressToEventRegistrations extends Migration
{
    public function up()
    {
        // Add ip_address column
        $fields = [
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
                'after' => 'member_id'
            ],
        ];

        $this->forge->addColumn('event_registrations', $fields);

        // Add index on ip_address
        $this->db->query('ALTER TABLE `event_registrations` ADD INDEX `idx_ip_address` (`ip_address`)');
    }

    public function down()
    {
        // Drop index first (must be done before dropping column)
        $this->db->query('ALTER TABLE `event_registrations` DROP INDEX `idx_ip_address`');

        // Drop column
        $this->forge->dropColumn('event_registrations', 'ip_address');
    }
}
