<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Group emails: one row per email an admin writes for an audience (e.g. an
 * event's registrants). Each recipient's copy is an ordinary email_queue row
 * pointing back here through email_queue.group_email_id.
 */
class CreateGroupEmails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'audience'        => ['type' => 'VARCHAR', 'constraint' => 50],   // key in Config\GroupEmail::$audiences
            'audience_ref'    => ['type' => 'INT', 'unsigned' => true, 'null' => true], // e.g. event id
            'audience_label'  => ['type' => 'VARCHAR', 'constraint' => 255],  // kept in case the event goes
            'filter_json'     => ['type' => 'TEXT', 'null' => true],
            'subject'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'body_html'       => ['type' => 'MEDIUMTEXT', 'null' => true],
            'wrap_layout'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'reply_to'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'], // draft, queued, cancelled
            'recipient_count' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'skipped_count'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'cancelled_count' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'test_count'      => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'last_test_to'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'last_test_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_by'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_by_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'queued_at'       => ['type' => 'DATETIME', 'null' => true],
            'cancelled_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['audience', 'audience_ref']);
        $this->forge->createTable('group_emails', true);

        $this->forge->addColumn('email_queue', [
            'group_email_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'related_id'],
        ]);
        $this->forge->addKey('group_email_id');
        $this->forge->processIndexes('email_queue');
    }

    public function down()
    {
        $this->forge->dropKey('email_queue', 'email_queue_group_email_id');
        $this->forge->dropColumn('email_queue', 'group_email_id');
        $this->forge->dropTable('group_emails', true);
    }
}
