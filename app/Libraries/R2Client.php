<?php

namespace App\Libraries;

use Config\R2;
use RuntimeException;

/**
 * Minimal client for Cloudflare R2's S3-compatible API.
 *
 * Only the four calls the gallery needs, signed with AWS Signature Version 4.
 * Written by hand rather than pulling in aws/aws-sdk-php, which is thousands
 * of files — Hostinger caps the number of files an account may hold.
 *
 * Failures throw RuntimeException; callers decide whether to retry.
 */
class R2Client
{
    private const REGION  = 'auto';
    private const SERVICE = 's3';

    /** S3's DeleteObjects limit per request. */
    private const DELETE_BATCH = 1000;

    public function __construct(protected R2 $config)
    {
    }

    public function isConfigured(): bool
    {
        return $this->config->isConfigured();
    }

    public function publicUrl(string $key): string
    {
        return $this->config->publicUrlFor($key);
    }

    public function put(string $key, string $body, string $contentType, string $cacheControl): void
    {
        $this->send('PUT', $key, [], $body, [
            'content-type'  => $contentType,
            'cache-control' => $cacheControl,
        ]);
    }

    public function get(string $key): string
    {
        return $this->send('GET', $key, [], '');
    }

    public function delete(string $key): void
    {
        $this->send('DELETE', $key, [], '');
    }

    /**
     * Delete many objects in as few requests as possible. Keys that do not
     * exist are not an error.
     *
     * @param list<string> $keys
     */
    public function deleteMany(array $keys): void
    {
        foreach (array_chunk(array_values(array_unique($keys)), self::DELETE_BATCH) as $chunk) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?><Delete><Quiet>true</Quiet>';
            foreach ($chunk as $key) {
                $xml .= '<Object><Key>' . htmlspecialchars($key, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</Key></Object>';
            }
            $xml .= '</Delete>';

            // Quiet mode lists only the failures.
            $response = $this->send('POST', '', ['delete' => ''], $xml, [
                'content-type' => 'application/xml',
                'content-md5'  => base64_encode(md5($xml, true)),
            ]);

            if (str_contains($response, '<Error>')) {
                throw new RuntimeException('R2 could not delete some objects: ' . substr($response, 0, 500));
            }
        }
    }

    /**
     * Send one signed request to the bucket and return the response body.
     *
     * @param array<string, string> $query
     * @param array<string, string> $headers lower-case names
     */
    protected function send(string $method, string $key, array $query, string $body, array $headers = []): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('R2 storage is not configured — set the r2.* values in .env.');
        }

        $host        = $this->config->endpointHost();
        $path        = '/' . $this->config->bucket . ($key === '' ? '' : '/' . ltrim($key, '/'));
        $payloadHash = hash('sha256', $body);
        $amzDate     = gmdate('Ymd\THis\Z');

        $headers += [
            'host'                 => $host,
            'x-amz-content-sha256' => $payloadHash,
            'x-amz-date'           => $amzDate,
        ];

        $headers['authorization'] = self::authorization(
            $method,
            $path,
            $query,
            $headers,
            $payloadHash,
            $this->config->accessKeyId,
            $this->config->secretAccessKey,
            self::REGION,
            self::SERVICE,
            $amzDate,
        );

        // curl sets Host itself from the URL; it is only needed for signing.
        unset($headers['host']);

        $url = 'https://' . $host . self::canonicalPath($path)
            . ($query === [] ? '' : '?' . self::canonicalQuery($query));

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_HTTPHEADER     => array_map(
                static fn(string $name, string $value): string => $name . ': ' . $value,
                array_keys($headers),
                $headers,
            ),
        ]);

        if ($body !== '') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($curl);
        $status   = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error    = curl_error($curl);

        if ($response === false) {
            throw new RuntimeException("R2 {$method} {$key} failed: {$error}");
        }

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException("R2 {$method} {$key} returned HTTP {$status}: " . substr((string) $response, 0, 500));
        }

        return (string) $response;
    }

    /**
     * AWS Signature Version 4 Authorization header value.
     *
     * Every header passed in is signed, so it must also be sent unchanged.
     *
     * @param array<string, string> $query
     * @param array<string, string> $headers
     */
    public static function authorization(
        string $method,
        string $path,
        array $query,
        array $headers,
        string $payloadHash,
        string $accessKeyId,
        string $secretAccessKey,
        string $region,
        string $service,
        string $amzDate,
    ): string {
        $canonicalHeaders = [];
        foreach ($headers as $name => $value) {
            $canonicalHeaders[strtolower($name)] = preg_replace('/\s+/', ' ', trim($value));
        }
        ksort($canonicalHeaders, SORT_STRING);

        $signedHeaders = implode(';', array_keys($canonicalHeaders));

        $canonicalRequest = implode("\n", [
            $method,
            self::canonicalPath($path),
            self::canonicalQuery($query),
            implode('', array_map(
                static fn(string $name, string $value): string => "{$name}:{$value}\n",
                array_keys($canonicalHeaders),
                $canonicalHeaders,
            )),
            $signedHeaders,
            $payloadHash,
        ]);

        $date  = substr($amzDate, 0, 8);
        $scope = "{$date}/{$region}/{$service}/aws4_request";

        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256',
            $amzDate,
            $scope,
            hash('sha256', $canonicalRequest),
        ]);

        $signingKey = hash_hmac('sha256', 'aws4_request',
            hash_hmac('sha256', $service,
                hash_hmac('sha256', $region,
                    hash_hmac('sha256', $date, 'AWS4' . $secretAccessKey, true),
                true),
            true),
        true);

        $signature = hash_hmac('sha256', $stringToSign, $signingKey);

        return "AWS4-HMAC-SHA256 Credential={$accessKeyId}/{$scope}, SignedHeaders={$signedHeaders}, Signature={$signature}";
    }

    /** S3 encodes each path segment once; the slashes between them stay. */
    private static function canonicalPath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    }

    /** @param array<string, string> $query */
    private static function canonicalQuery(array $query): string
    {
        ksort($query, SORT_STRING);

        return implode('&', array_map(
            static fn(string $key, string $value): string => rawurlencode($key) . '=' . rawurlencode($value),
            array_keys($query),
            $query,
        ));
    }
}
