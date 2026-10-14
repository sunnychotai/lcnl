<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Photo gallery settings. Storage credentials live in Config\R2.
 *
 * Overridable from .env with the `gallery.` prefix.
 */
class Gallery extends BaseConfig
{
    /** Photos per page on an album, and per "Load more". */
    public int $perPage = 40;

    /** Albums per page on /gallery. */
    public int $albumsPerPage = 24;

    /** Pre-filled on new albums; editable per album. */
    public string $defaultCredit = 'Photos by NiteshTakePhotos for LCNL';

    /**
     * Image variants written to R2, keyed by suffix. Each is scaled to fit
     * inside width x height, never enlarged. "web" is what the lightbox shows
     * and what visitors download; "thumb" fills the album grid.
     *
     * @var array<string, array{width: int, height: int, quality: int}>
     */
    public array $variants = [
        'web'   => ['width' => 1600, 'height' => 1600, 'quality' => 82],
        'thumb' => ['width' => 600,  'height' => 1200, 'quality' => 72],
    ];

    /**
     * The browser resizes to the web size before uploading, so a real upload
     * is well under 2 MB. This cap only stops something unexpected reaching GD.
     */
    public int $maxUploadBytes = 12 * 1024 * 1024;

    /** Larger images are refused rather than risk exhausting memory in GD. */
    public int $maxSourcePixels = 40_000_000;

    /** Files sent to R2 never change — a new photo always gets a new key. */
    public string $cacheControl = 'public, max-age=31536000, immutable';
}
