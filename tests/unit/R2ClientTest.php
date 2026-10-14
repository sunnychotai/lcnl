<?php

namespace Tests\Unit;

use App\Libraries\R2Client;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * The signer is checked against the worked examples in AWS's own Signature
 * Version 4 documentation for S3 ("Examples: Signature Calculations"), so a
 * pass here means R2 will accept the same signatures.
 *
 * @internal
 */
final class R2ClientTest extends CIUnitTestCase
{
    private const ACCESS_KEY = 'AKIAIOSFODNN7EXAMPLE';
    private const SECRET_KEY = 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY';
    private const EMPTY_HASH = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';

    public function testSignsGetObjectExample(): void
    {
        $auth = R2Client::authorization(
            'GET',
            '/test.txt',
            [],
            [
                'Host'                 => 'examplebucket.s3.amazonaws.com',
                'Range'                => 'bytes=0-9',
                'x-amz-content-sha256' => self::EMPTY_HASH,
                'x-amz-date'           => '20130524T000000Z',
            ],
            self::EMPTY_HASH,
            self::ACCESS_KEY,
            self::SECRET_KEY,
            'us-east-1',
            's3',
            '20130524T000000Z',
        );

        $this->assertSame(
            'AWS4-HMAC-SHA256 Credential=AKIAIOSFODNN7EXAMPLE/20130524/us-east-1/s3/aws4_request, '
            . 'SignedHeaders=host;range;x-amz-content-sha256;x-amz-date, '
            . 'Signature=f0e8bdb87c964420e857bd35b5d6ed310bd44f0170aba48dd91039c6036bdb41',
            $auth,
        );
    }

    public function testSignsPutObjectExampleWithEncodedKey(): void
    {
        $payloadHash = hash('sha256', 'Welcome to Amazon S3.');

        $auth = R2Client::authorization(
            'PUT',
            '/test$file.text',
            [],
            [
                'Host'                 => 'examplebucket.s3.amazonaws.com',
                'Date'                 => 'Fri, 24 May 2013 00:00:00 GMT',
                'x-amz-date'           => '20130524T000000Z',
                'x-amz-storage-class'  => 'REDUCED_REDUNDANCY',
                'x-amz-content-sha256' => $payloadHash,
            ],
            $payloadHash,
            self::ACCESS_KEY,
            self::SECRET_KEY,
            'us-east-1',
            's3',
            '20130524T000000Z',
        );

        $this->assertStringEndsWith(
            'SignedHeaders=date;host;x-amz-content-sha256;x-amz-date;x-amz-storage-class, '
            . 'Signature=98ad721746da40c64f1a55b78f14c238d841ea1380cd77a1b5971af0ece108bd',
            $auth,
        );
    }

    public function testRefusesToSendWithoutConfiguration(): void
    {
        $config            = new \Config\R2();
        $config->accountId = '';
        $client            = new R2Client($config);

        $this->expectException(\RuntimeException::class);
        $client->put('a.webp', 'x', 'image/webp', 'no-cache');
    }
}
