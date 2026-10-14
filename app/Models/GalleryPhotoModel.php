<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryPhotoModel extends Model
{
    protected $table         = 'gallery_photos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'album_id', 'source_hash', 'original_name', 'storage_key',
        'width', 'height', 'sort', 'uploaded_by',
    ];

    /** Photos of an album in display order. */
    public function forAlbum(int $albumId): static
    {
        return $this->where('album_id', $albumId)->orderBy('sort', 'ASC')->orderBy('id', 'ASC');
    }
}
