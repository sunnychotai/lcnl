<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueIndexToSlug extends Migration
{
    public function up()
    {
        $this->forge->addColumn('events', [
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);

        $this->forge->addUniqueKey('slug');
        $this->forge->processIndexes('events');
    }
    public function down()
    {
        $this->forge->dropKey('events', 'slug');
    }
}
