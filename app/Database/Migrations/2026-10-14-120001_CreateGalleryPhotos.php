<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGalleryPhotos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'album_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            // SHA-256 of the file as it was on the uploader's computer, before
            // the browser resized it. Re-dropping a folder skips what is
            // already here, which is how an interrupted upload resumes.
            'source_hash' => [
                'type'       => 'CHAR',
                'constraint' => 64,
            ],
            'original_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            // R2 key without the variant suffix, e.g. albums/12/3f9c…; the
            // files are {key}-web.webp and {key}-thumb.webp.
            'storage_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            // Of the web variant. The grid lays out from the ratio, so the page
            // does not jump as images load.
            'width' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'height' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'sort' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'uploaded_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['album_id', 'source_hash']);
        $this->forge->addKey(['album_id', 'sort']);
        $this->forge->addForeignKey('album_id', 'gallery_albums', 'id', '', 'CASCADE');
        $this->forge->createTable('gallery_photos');
    }

    public function down()
    {
        $this->forge->dropTable('gallery_photos', true);
    }
}
