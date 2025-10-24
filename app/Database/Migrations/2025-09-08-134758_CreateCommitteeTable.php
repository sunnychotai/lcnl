<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommitteeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'firstname'  => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'surname'    => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'email'      => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'role'       => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'display_order' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'image'      => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true, // link to image
            ],
            'url'        => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true, // external link (LinkedIn, etc.)
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('committee');
    }

    public function down()
    {
        $this->forge->dropTable('committee');
    }
}
