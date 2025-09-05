<?php
$logDir = '/home/u485920306/domains/aricer.com/lcnl/writable/logs';

if (!is_dir($logDir)) {
    die("❌ Logs directory missing: $logDir");
}

$files = glob("$logDir/log-*.php");
if (!$files) {
    die("⚠️ No log files found in $logDir");
}

$latest = end($files);
echo "📄 Showing last 50 lines of $latest:<br><pre>";
echo implode("", array_slice(file($latest), -50));
echo "</pre>";
