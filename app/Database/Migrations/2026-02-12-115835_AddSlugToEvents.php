<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlugToEvents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('events', [
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
                'unique' => true,
                'after' => 'title',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('events', ['slug']);
    }

}
