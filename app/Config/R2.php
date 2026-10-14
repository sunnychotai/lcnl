<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Cloudflare R2 bucket that holds the photo gallery's images.
 *
 * Every value comes from .env with the `r2.` prefix. Local work uses the
 * `lcnl-gallery-dev` bucket and production `lcnl-gallery`, each with its own
 * API token scoped to that bucket alone, so a leaked development .env cannot
 * touch live photos.
 */
class R2 extends BaseConfig
{
    /** Cloudflare account ID — R2 > Overview, right-hand panel. */
    public string $accountId = '';

    /** From an R2 API token with Object Read & Write on this bucket only. */
    public string $accessKeyId = '';

    public string $secretAccessKey = '';

    public string $bucket = '';

    /** The bucket's custom domain, e.g. https://photos.lcnl.co.uk */
    public string $publicUrl = '';

    public function isConfigured(): bool
    {
        return $this->accountId !== ''
            && $this->accessKeyId !== ''
            && $this->secretAccessKey !== ''
            && $this->bucket !== ''
            && $this->publicUrl !== '';
    }

    /** S3-compatible API endpoint for the account. */
    public function endpointHost(): string
    {
        return $this->accountId . '.r2.cloudflarestorage.com';
    }

    public function publicUrlFor(string $key): string
    {
        return rtrim($this->publicUrl, '/') . '/' . ltrim($key, '/');
    }
}
