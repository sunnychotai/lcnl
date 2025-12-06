<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailToMemberFamily extends Migration
{
    public function up()
    {
        // Add nullable email column + a small index for lookups
        $this->forge->addColumn('member_family', [
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 191,
                'null'       => true,
                'after'      => 'name', // put it near name; move if you prefer
            ],
        ]);

        // Optional index (helpful if you’ll search by email)
        $this->db->query('CREATE INDEX IF NOT EXISTS member_family_email_idx ON member_family (email(64))');
        // If your MySQL doesn’t support the IF NOT EXISTS above, you can skip it.
    }

    public function down()
    {
        $this->forge->dropColumn('member_family', 'email');

        // Optional: drop index if you created one explicitly without IF NOT EXISTS
        // $this->db->query('DROP INDEX member_family_email_idx ON member_family');
    }
}
