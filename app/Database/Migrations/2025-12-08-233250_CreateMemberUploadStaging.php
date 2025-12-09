<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMemberUploadStaging extends Migration
{
    public function up()
    {
        // Raw staging table to preserve uploads and control processing
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'source_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'last_name' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'address1' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'address2' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'city' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'postcode' => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'mobile' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],

            // member_family.* in the upload
            'family_name' => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'family_email' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'family_telephone' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],

            // processing control
            'new_member_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'imported', 'error'], 'default' => 'pending'],
            'error_message' => ['type' => 'TEXT', 'null' => true],
            'processed_at' => ['type' => 'DATETIME', 'null' => true],

            // audit
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
            'updated_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('new_member_id');
        $this->forge->createTable('member_upload_staging', true);
    }

    public function down()
    {
        $this->forge->dropTable('member_upload_staging', true);
    }
}
