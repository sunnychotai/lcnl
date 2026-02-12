<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueIndexToSlug extends Migration
{
    public function up()
    {


        $this->forge->addUniqueKey('slug');
        $this->forge->processIndexes('events');
    }
    public function down()
    {
        $this->forge->dropKey('events', 'slug');
    }
}
