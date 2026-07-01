<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixIsValidEmailDefault extends Migration
{
    public function up()
    {
        $this->db->query(
            "ALTER TABLE members MODIFY COLUMN is_valid_email TINYINT(1) NOT NULL DEFAULT 1"
        );
    }

    public function down()
    {
        $this->db->query(
            "ALTER TABLE members MODIFY COLUMN is_valid_email TINYINT(1) NOT NULL DEFAULT 0"
        );
    }
}
