<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFaqsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'faq_group' => ['type' => 'VARCHAR', 'constraint' => 100], // e.g. LCNL, Membership, Bereavement
            'question'  => ['type' => 'TEXT'],
            'answer'    => ['type' => 'TEXT'],
            'faq_order' => ['type' => 'INT', 'constraint' => 5, 'default' => 0],
            'valid'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'=> ['type' => 'DATETIME', 'null' => true],
            'updated_at'=> ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('faqs');
    }

    public function down()
    {
        $this->forge->dropTable('faqs');
    }
}
