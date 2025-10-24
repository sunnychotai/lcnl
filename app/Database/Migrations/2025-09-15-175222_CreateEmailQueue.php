<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailQueue extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'to_email'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'to_name'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'subject'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'body_html'    => ['type' => 'MEDIUMTEXT', 'null' => true],
            'body_text'    => ['type' => 'MEDIUMTEXT', 'null' => true],
            'headers_json' => ['type' => 'TEXT', 'null' => true], // optional extra headers (JSON)
            'priority'     => ['type' => 'TINYINT', 'default' => 5], // 1=highest, 9=lowest
            'status'       => ["type" => "ENUM('pending','sending','sent','failed')", 'default' => 'pending'],
            'attempts'     => ['type' => 'TINYINT', 'default' => 0],
            'last_error'   => ['type' => 'TEXT', 'null' => true],
            'scheduled_at' => ['type' => 'DATETIME', 'null' => true], // null=send asap
            'sent_at'      => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'priority']);
        $this->forge->addKey('scheduled_at');
        $this->forge->createTable('email_queue', true);
    }

    public function down()
    {
        $this->forge->dropTable('email_queue', true);
    }
}
