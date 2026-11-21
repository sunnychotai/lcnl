<?php

namespace App\Libraries;

use Config\Turnstile as TurnstileConfig;

/**
 * Checks a Cloudflare Turnstile token posted by a form that includes
 * view('partials/turnstile').
 *
 * With no keys configured the check is off and verify() always passes. If
 * Cloudflare can't be reached it also passes (and logs), so an outage at
 * Cloudflare never stops people contacting LCNL; the form's other spam
 * defences still apply.
 */
class Turnstile
{
    public const FIELD = 'cf-turnstile-response';

    private TurnstileConfig $config;

    public function __construct(?TurnstileConfig $config = null)
    {
        $this->config = $config ?? config('Turnstile');
    }

    public function enabled(): bool
    {
        return $this->config->siteKey !== '' && $this->config->secretKey !== '';
    }

    public function siteKey(): string
    {
        return $this->config->siteKey;
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        if (!$this->enabled()) {
            return true;
        }

        $token = trim((string) $token);
        if ($token === '' || strlen($token) > 2048) {
            return false;
        }

        try {
            $response = service('curlrequest')->post($this->config->verifyUrl, [
                'form_params' => array_filter([
                    'secret'   => $this->config->secretKey,
                    'response' => $token,
                    'remoteip' => $ip,
                ]),
                'timeout'     => $this->config->timeout,
                'http_errors' => false,
            ]);

            $result = json_decode((string) $response->getBody(), true);
        } catch (\Throwable $e) {
            log_message('warning', 'Turnstile: could not reach Cloudflare, letting the submission through: ' . $e->getMessage());

            return true;
        }

        if (!is_array($result) || !array_key_exists('success', $result)) {
            log_message('warning', 'Turnstile: unexpected reply from Cloudflare, letting the submission through.');

            return true;
        }

        return $result['success'] === true;
    }
}
