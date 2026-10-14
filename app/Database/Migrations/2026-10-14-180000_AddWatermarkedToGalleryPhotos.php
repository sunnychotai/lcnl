<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWatermarkedToGalleryPhotos extends Migration
{
    public function up()
    {
        // Photos already uploaded start at 0, so the admin screen can offer to
        // watermark them.
        $this->forge->addColumn('gallery_photos', [
            'watermarked' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'height',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('gallery_photos', 'watermarked');
    }
}
