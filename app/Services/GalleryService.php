<?php

namespace App\Services;

use App\Libraries\GalleryImageProcessor;
use App\Libraries\R2Client;
use App\Models\GalleryAlbumModel;
use App\Models\GalleryPhotoModel;
use Config\Gallery;
use Config\Services;
use Throwable;

/**
 * Everything that touches both the gallery tables and R2 goes through here,
 * so a photo row never exists without its files, and the album's photo count
 * and cover stay correct.
 */
class GalleryService
{
    private Gallery $config;
    private R2Client $storage;
    private GalleryAlbumModel $albums;
    private GalleryPhotoModel $photos;

    public function __construct(?R2Client $storage = null)
    {
        $this->config  = config('Gallery');
        $this->storage = $storage ?? Services::r2();
        $this->albums  = new GalleryAlbumModel();
        $this->photos  = new GalleryPhotoModel();
    }

    public function storageReady(): bool
    {
        return $this->storage->isConfigured();
    }

    /**
     * Which of these source hashes the album already holds.
     *
     * @param list<string> $hashes
     *
     * @return list<string>
     */
    public function existingHashes(int $albumId, array $hashes): array
    {
        $hashes = array_values(array_filter($hashes, [self::class, 'isValidHash']));

        if ($hashes === []) {
            return [];
        }

        return array_column(
            $this->photos->select('source_hash')
                ->where('album_id', $albumId)
                ->whereIn('source_hash', $hashes)
                ->findAll(),
            'source_hash',
        );
    }

    /**
     * Process one photo, store its variants in R2 and record it.
     *
     * @return array{status: 'added'|'duplicate', photo?: array}
     *
     * @throws \InvalidArgumentException when the file is not a usable photo
     * @throws \RuntimeException         when storage fails
     */
    public function addPhoto(int $albumId, string $path, string $sourceHash, ?string $originalName, int $sort, ?string $uploadedBy): array
    {
        if (! self::isValidHash($sourceHash)) {
            throw new \InvalidArgumentException('Missing or malformed file hash.');
        }

        if ($this->existingHashes($albumId, [$sourceHash]) !== []) {
            return ['status' => 'duplicate'];
        }

        $variants = (new GalleryImageProcessor($this->config))->process($path);

        // A random tail means a photo deleted and later re-uploaded gets a new
        // URL, so a copy still held in the CDN cache is never served for it.
        $key     = 'albums/' . $albumId . '/' . substr($sourceHash, 0, 16) . bin2hex(random_bytes(4));
        $written = [];

        try {
            foreach ($variants as $name => $variant) {
                $objectKey = self::variantKey($key, $name);
                $this->storage->put($objectKey, $variant['body'], 'image/webp', $this->config->cacheControl);
                $written[] = $objectKey;
            }

            $id = $this->photos->insert([
                'album_id'      => $albumId,
                'source_hash'   => $sourceHash,
                'original_name' => $originalName === null ? null : mb_substr($originalName, 0, 255),
                'storage_key'   => $key,
                'width'         => $variants['web']['width'],
                'height'        => $variants['web']['height'],
                'sort'          => max(0, $sort),
                'uploaded_by'   => $uploadedBy,
            ], true);
        } catch (Throwable $e) {
            // Two tabs uploading the same file can both pass the duplicate
            // check; the unique key stops the second, and its files go.
            $this->deleteObjectsQuietly($written);

            if ($this->existingHashes($albumId, [$sourceHash]) !== []) {
                return ['status' => 'duplicate'];
            }

            throw $e;
        }

        $this->refreshAlbum($albumId);

        return ['status' => 'added', 'photo' => $this->photos->find($id)];
    }

    /**
     * Delete photos from an album, files first. Returns how many went.
     *
     * @param list<int> $photoIds
     */
    public function deletePhotos(int $albumId, array $photoIds): int
    {
        $photoIds = array_values(array_filter(array_map('intval', $photoIds)));

        if ($photoIds === []) {
            return 0;
        }

        $rows = $this->photos->where('album_id', $albumId)->whereIn('id', $photoIds)->findAll();

        if ($rows === []) {
            return 0;
        }

        // Files first: if R2 fails the rows stay, and the admin can retry.
        $this->storage->deleteMany($this->objectKeys($rows));
        $this->photos->whereIn('id', array_column($rows, 'id'))->delete();
        $this->refreshAlbum($albumId);

        return count($rows);
    }

    public function deleteAlbum(int $albumId): void
    {
        $rows = $this->photos->where('album_id', $albumId)->findAll();

        if ($rows !== []) {
            $this->storage->deleteMany($this->objectKeys($rows));
            $this->photos->where('album_id', $albumId)->delete();
        }

        $this->albums->delete($albumId);
    }

    /**
     * Save a new display order. Photos not listed keep their place after the
     * listed ones.
     *
     * @param list<int> $photoIds in the order they should appear
     */
    public function reorder(int $albumId, array $photoIds): void
    {
        $owned = array_flip(array_column(
            $this->photos->select('id')->where('album_id', $albumId)->findAll(),
            'id',
        ));

        $position = 0;
        foreach ($photoIds as $id) {
            $id = (int) $id;
            if (isset($owned[$id])) {
                $this->photos->update($id, ['sort' => ++$position]);
            }
        }
    }

    public function setCover(int $albumId, int $photoId): bool
    {
        if (! $this->photos->where('album_id', $albumId)->where('id', $photoId)->first()) {
            return false;
        }

        $this->albums->update($albumId, ['cover_photo_id' => $photoId]);

        return true;
    }

    /** Next free sort position, so a new batch lands after what is there. */
    public function nextSort(int $albumId): int
    {
        $row = $this->photos->selectMax('sort')->where('album_id', $albumId)->first();

        return (int) ($row['sort'] ?? 0) + 1;
    }

    /** Recount photos and make sure the cover still exists. */
    public function refreshAlbum(int $albumId): void
    {
        $album = $this->albums->find($albumId);

        if (! $album) {
            return;
        }

        $count = $this->photos->where('album_id', $albumId)->countAllResults();
        $cover = $album['cover_photo_id'];

        if ($cover && ! $this->photos->where('album_id', $albumId)->where('id', $cover)->countAllResults()) {
            $cover = null;
        }

        if (! $cover && $count > 0) {
            $cover = $this->photos->forAlbum($albumId)->first()['id'];
        }

        $this->albums->update($albumId, ['photo_count' => $count, 'cover_photo_id' => $cover]);
    }

    /**
     * Public URLs for a photo row.
     *
     * @return array{web: string, thumb: string}
     */
    public function urls(array $photo): array
    {
        return [
            'web'   => $this->storage->publicUrl(self::variantKey($photo['storage_key'], 'web')),
            'thumb' => $this->storage->publicUrl(self::variantKey($photo['storage_key'], 'thumb')),
        ];
    }

    /** The web-size WebP bytes, for downloads. */
    public function fetchWeb(array $photo): string
    {
        return $this->storage->get(self::variantKey($photo['storage_key'], 'web'));
    }

    public static function variantKey(string $key, string $variant): string
    {
        return "{$key}-{$variant}.webp";
    }

    public static function isValidHash(mixed $hash): bool
    {
        return is_string($hash) && preg_match('/^[a-f0-9]{64}$/', $hash) === 1;
    }

    /** @return list<string> */
    private function objectKeys(array $rows): array
    {
        $keys = [];
        foreach ($rows as $row) {
            foreach (array_keys($this->config->variants) as $variant) {
                $keys[] = self::variantKey($row['storage_key'], $variant);
            }
        }

        return $keys;
    }

    /** @param list<string> $keys */
    private function deleteObjectsQuietly(array $keys): void
    {
        if ($keys === []) {
            return;
        }

        try {
            $this->storage->deleteMany($keys);
        } catch (Throwable $e) {
            log_message('error', 'Gallery: could not remove orphaned R2 objects {keys}: {msg}', [
                'keys' => implode(', ', $keys),
                'msg'  => $e->getMessage(),
            ]);
        }
    }
}
