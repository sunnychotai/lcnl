<?php
trigger_error("Test log entry", E_USER_ERROR);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Config\Paths;
use CodeIgniter\Boot;

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
$localPaths   = realpath(FCPATH . '../app/Config/Paths.php');            // Local dev
$remotePaths  = realpath(FCPATH . '../../lcnl/app/Config/Paths.php'); // Hostinger

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
if (is_dir(FCPATH . '../system')) {
    $paths->systemDirectory = realpath(FCPATH . '../system'); // legacy local
} elseif (is_dir(FCPATH . '../vendor/codeigniter4/framework/system')) {
    $paths->systemDirectory = realpath(FCPATH . '../vendor/codeigniter4/framework/system'); // local composer
} elseif (is_dir(FCPATH . '../../lcnl/vendor/codeigniter4/framework/system')) {
    $paths->systemDirectory = realpath(FCPATH . '../../lcnl/vendor/codeigniter4/framework/system'); // Hostinger
} else {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo "❌ Could not locate system directory";
    exit(1);
}

// ---------------------------------------------------------------
// BOOT THE FRAMEWORK
// ---------------------------------------------------------------
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
