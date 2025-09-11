<?php
/**
 * Case sensitivity checker for CodeIgniter 4 apps
 * Scans views for asset references and compares with filesystem
 */

$baseDir = __DIR__;
$publicDir = $baseDir . '\public';

$errors = [];

/**
 * Recursively scan files
 */
function scanFiles($dir, $exts = ['php','html','css','js']) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $files = [];
    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        if (in_array($ext, $exts)) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

// 1. Collect all actual files under /public
$actualFiles = [];
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($publicDir));
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $relPath = str_replace($publicDir, '', $file->getPathname());
    $actualFiles[strtolower($relPath)] = $relPath;
}

// 2. Scan view files for "assets/"
$viewFiles = scanFiles($baseDir . '/app/Views');
foreach ($viewFiles as $vf) {
    $content = file_get_contents($vf);
    preg_match_all('/assets\/[a-zA-Z0-9_\/\.\-]+/', $content, $matches);
    foreach ($matches[0] as $match) {
        $normalized = '/' . $match;
        $key = strtolower($normalized);
        if (!isset($actualFiles[$key])) {
            $errors[] = [
                'file' => $vf,
                'reference' => $match,
                'suggestion' => isset($actualFiles[$key]) ? $actualFiles[$key] : 'NOT FOUND'
            ];
        }
    }
}

// Report
if (empty($errors)) {
    echo "✅ No case-sensitivity issues found.\n";
} else {
    echo "⚠️ Potential case mismatches:\n";
    foreach ($errors as $err) {
        echo "- In {$err['file']} → references '{$err['reference']}', ";
        echo "but actual is '{$err['suggestion']}'\n";
    }
}
