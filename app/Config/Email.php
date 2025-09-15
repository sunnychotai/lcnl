<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'sunny@aricer.com';
    public string $fromName   = 'Lohana Community North London';
    public string $recipients = '';

    public string $protocol   = 'smtp';
    public string $SMTPHost   = 'smtp.hostinger.com';
    public string $SMTPUser   = 'sunny@aricer.com';
    public string $SMTPPass   = ''; // <-- set via .env in PROD
    public int    $SMTPPort   = 465;
    public string $SMTPCrypto = 'ssl';

    public string $mailType   = 'html';
    public string $charset    = 'utf-8';
    public bool   $wordWrap   = true;
}
