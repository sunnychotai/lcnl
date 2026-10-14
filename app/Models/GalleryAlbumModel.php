<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryAlbumModel extends Model
{
    protected $table         = 'gallery_albums';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'title', 'slug', 'event_id', 'album_date', 'description', 'credit',
        'cover_photo_id', 'photo_count', 'is_published', 'created_by',
    ];

    /** Albums the public can see: published and not empty. */
    public function visible(): static
    {
        return $this->where('is_published', 1)->where('photo_count >', 0);
    }

    public function findVisibleBySlug(string $slug): ?array
    {
        return $this->visible()->where('slug', $slug)->first();
    }

    public function findVisibleForEvent(int $eventId): ?array
    {
        return $this->visible()
            ->where('event_id', $eventId)
            ->orderBy('album_date', 'DESC')
            ->first();
    }

    /** Lower-case, hyphenated, unique among albums. */
    public function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($source)), '-');
        $base = substr($base === '' ? 'album' : $base, 0, 150);
        $slug = $base;

        for ($n = 2; ; $n++) {
            $builder = $this->where('slug', $slug);
            if ($ignoreId !== null) {
                $builder->where('id !=', $ignoreId);
            }
            if ($builder->countAllResults() === 0) {
                return $slug;
            }
            $slug = $base . '-' . $n;
        }
    }
}
