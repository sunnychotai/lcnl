<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = '';
    public string $fromName   = '';
    public string $recipients = '';

    // use null so .env overrides properly
    public ?string $protocol   = null;
    public ?string $SMTPHost   = null;
    public ?string $SMTPUser   = null;
    public ?string $SMTPPass   = null; // set via .env in PROD
    public ?int    $SMTPPort   = null;
    public ?string $SMTPCrypto = null;

    public string $mailType   = 'html';
    public string $charset    = 'utf-8';
    public bool   $wordWrap   = true;
}
