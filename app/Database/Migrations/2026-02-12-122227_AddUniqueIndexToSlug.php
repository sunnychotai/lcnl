<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueIndexToSlug extends Migration
{
    public function up()
    {
        $this->forge->addKey('slug', true);
        $this->forge->processIndexes('events');
    }

    public function down()
    {
        $this->forge->dropKey('events', 'slug');
    }
}
