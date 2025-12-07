<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailToMemberFamily extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        // Check if column already exists
        $columnCheck = $db->query("
        SHOW COLUMNS FROM member_family LIKE 'email'
    ")->getResult();

        if (empty($columnCheck)) {
            $forge->addColumn('member_family', [
                'email' => [
                    'type' => 'VARCHAR',
                    'constraint' => 191,
                    'null' => true,
                ],
            ]);
        }
    }


    public function down()
    {
        $this->forge->dropColumn('member_family', 'email');

        // Optional: drop index if you created one explicitly without IF NOT EXISTS
        // $this->db->query('DROP INDEX member_family_email_idx ON member_family');
    }
}
