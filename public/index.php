<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use Config\Paths;

// ---------------------------------------------------------------
// CHECK PHP VERSION
// ---------------------------------------------------------------
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;
    exit(1);
}

// ---------------------------------------------------------------
// SET THE CURRENT DIRECTORY
// ---------------------------------------------------------------
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// ---------------------------------------------------------------
// LOCATE PATHS.PHP
// ---------------------------------------------------------------
// On localhost (XAMPP) the app folder is ../app
// On Hostinger it lives in ../../../lcnl/app
$localPaths   = realpath(FCPATH . '../app/Config/Paths.php');
$remotePaths  = realpath(FCPATH . '../../lcnl/app/Config/Paths.php');

if (is_file($localPaths)) {
    require $localPaths;
} elseif (is_file($remotePaths)) {
    require $remotePaths;
} else {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo "❌ Could not locate Paths.php";
    exit(1);
}

$paths = new Paths();

// ---------------------------------------------------------------
// LOCATE SYSTEM DIRECTORY
// ---------------------------------------------------------------
// Local (XAMPP): ../system OR ../vendor/codeigniter4/framework/system
// Hostinger: ../../../lcnl/vendor/codeigniter4/framework/system
if (is_dir(FCPATH . '../system')) {
    $paths->systemDirectory = realpath(FCPATH . '../system');
} elseif (is_dir(FCPATH . '../vendor/codeigniter4/framework/system')) {
    $paths->systemDirectory = realpath(FCPATH . '../vendor/codeigniter4/framework/system');
} elseif (is_dir(FCPATH . '../../lcnl/vendor/codeigniter4/framework/system')) {
    $paths->systemDirectory = realpath(FCPATH . '../../../lcnl/vendor/codeigniter4/framework/system');
} else {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo "❌ Could not locate system directory";
    exit(1);
}

// ---------------------------------------------------------------
// BOOT THE FRAMEWORK
// ---------------------------------------------------------------
var_dump($paths->systemDirectory);
exit;
require $paths->systemDirectory . '/Boot.php';

exit(\CodeIgniter\Boot\Boot::bootWeb($paths));
