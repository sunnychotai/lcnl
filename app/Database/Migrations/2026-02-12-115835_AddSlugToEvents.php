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
                'constraint' => 255,
                'null' => true,
                'after' => 'title',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('events', ['slug']);
    }

}
