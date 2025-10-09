<?php

// Define path constants
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', ROOTPATH . 'vendor/codeigniter4/framework/system' . DIRECTORY_SEPARATOR);
define('WRITEPATH', ROOTPATH . 'writable' . DIRECTORY_SEPARATOR);

// Load Composer autoloader
require ROOTPATH . 'vendor/autoload.php';

// Load environment bootstrap (if you're using .env)
if (file_exists(ROOTPATH . '.env')) {
    require_once SYSTEMPATH . 'Config/DotEnv.php';
    (new \CodeIgniter\Config\DotEnv(ROOTPATH))->load();
}

// Set environment
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', $_ENV['CI_ENVIRONMENT'] ?? 'production');
}

// Load Config\Services
$config = new \Config\Services();

// Send email
$email = \Config\Services::email();

$email->setTo('test@example.com');
$email->setFrom('no-reply@lcnl.local', 'LCNL');
$email->setSubject('Test Mail');
$email->setMessage('<p>Hello world from LCNL test.</p>');

if ($email->send()) {
    echo "✅ Email sent successfully\n";
} else {
    echo "❌ Failed to send email:\n";
    echo $email->printDebugger(['headers', 'subject', 'body']);
}