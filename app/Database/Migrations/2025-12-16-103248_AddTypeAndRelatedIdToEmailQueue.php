<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeAndRelatedIdToEmailQueue extends Migration
{
    public function up()
    {
        // Add columns (nullable so existing rows don’t break)
        $fields = [
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'subject',
            ],
            'related_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
                'after' => 'type',
            ],
        ];

        $this->forge->addColumn('email_queue', $fields);

        // Helpful index for filtering and lookups
        $this->forge->addKey(['type', 'related_id'], false, false, 'type_related');
    }

    public function down()
    {
        // Drop index first (safe try-catch in case it wasn’t created)
        try {
            $this->forge->dropKey('email_queue', 'type_related');
        } catch (\Throwable $e) {
            // ignore
        }

        $this->forge->dropColumn('email_queue', 'related_id');
        $this->forge->dropColumn('email_queue', 'type');
    }
}
