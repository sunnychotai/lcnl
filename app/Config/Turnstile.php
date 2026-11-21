<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Cloudflare Turnstile, the "are you human?" check on public forms.
 *
 * Keys come from .env:
 *   turnstile.siteKey   = …
 *   turnstile.secretKey = …
 * Without both keys the check is off and forms work as before.
 *
 * Cloudflare's test keys, for local use: site key 1x00000000000000000000AA
 * and secret key 1x0000000000000000000000000000000AA always pass.
 */
class Turnstile extends BaseConfig
{
    public string $siteKey   = '';
    public string $secretKey = '';

    public string $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /** Seconds to wait for Cloudflare before letting the submission through. */
    public int $timeout = 5;
}
