<?php

// Test 1: PHP runtime working?
echo "PHP is running! Version: " . phpversion() . "<br>";

// Test 2: Can we find the vendor folder?
$vendorPath = __DIR__ . '/../vendor';
echo "Vendor exists: " . (is_dir($vendorPath) ? 'YES' : 'NO') . "<br>";

// Test 3: Can we find public/index.php?
$publicPath = __DIR__ . '/../public';
echo "Public/index.php exists: " . (is_file($publicPath . '/index.php') ? 'YES' : 'NO') . "<br>";

// Test 4: bootstrap/app.php exists?
$bootstrapPath = __DIR__ . '/../bootstrap/app.php';
echo "Bootstrap/app.php exists: " . (is_file($bootstrapPath) ? 'YES' : 'NO') . "<br>";

// Test 5: APP_KEY set?
echo "APP_KEY set: " . (!empty($_ENV['APP_KEY']) || !empty(getenv('APP_KEY')) ? 'YES - ' . substr(getenv('APP_KEY'), 0, 20) . '...' : 'NO - MISSING!') . "<br>";

echo "<hr>If all YES above, now loading Laravel...<br>";

// Try to load Laravel
ini_set('display_errors', '1');
error_reporting(E_ALL);

$url = $_SERVER['REQUEST_URI'] ?? '/';
$filePath = $publicPath . parse_url($url, PHP_URL_PATH);

if (is_file($filePath)) {
    return false;
}

$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_PORT'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 443 : 80;

require $publicPath . '/index.php';
