<?php

namespace Tests\Support\Libraries;

use App\Libraries\R2Client;
use RuntimeException;

/**
 * In-memory stand-in for R2, so gallery tests never touch Cloudflare.
 */
class FakeR2Client extends R2Client
{
    /** @var array<string, array{body: string, type: string, cache: string}> */
    public array $objects = [];

    public bool $failPuts = false;

    public function __construct()
    {
        parent::__construct(new \Config\R2());
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function publicUrl(string $key): string
    {
        return 'https://photos.test/' . $key;
    }

    public function put(string $key, string $body, string $contentType, string $cacheControl): void
    {
        if ($this->failPuts) {
            throw new RuntimeException('R2 PUT failed');
        }

        $this->objects[$key] = ['body' => $body, 'type' => $contentType, 'cache' => $cacheControl];
    }

    public function get(string $key): string
    {
        if (! isset($this->objects[$key])) {
            throw new RuntimeException('R2 GET returned HTTP 404');
        }

        return $this->objects[$key]['body'];
    }

    public function delete(string $key): void
    {
        unset($this->objects[$key]);
    }

    public function deleteMany(array $keys): void
    {
        foreach ($keys as $key) {
            unset($this->objects[$key]);
        }
    }
}
