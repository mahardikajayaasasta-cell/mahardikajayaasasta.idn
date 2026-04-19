<?php

// Vercel PHP Runtime entry point untuk Laravel
// Mencegah duplicate constant 'LARAVEL_START'
if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

$url = $_SERVER['REQUEST_URI'] ?? '/';
$publicPath = __DIR__ . '/../public';
$filePath = $publicPath . parse_url($url, PHP_URL_PATH);

if (is_file($filePath)) {
    return false;
}

// Fix environment & paths untuk Vercel (Read-Only Filesystem)
$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_PORT'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 443 : 80;

// Set direktori penyimpanan ke /tmp karena Vercel serverless functions bersifat read-only
$app = require __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($_ENV['APP_STORAGE'] ?? '/tmp/storage');
$app->useBootstrapPath($_ENV['APP_BOOTSTRAP'] ?? '/tmp/bootstrap');

// Buat direktori temporary yang dibutuhkan Laravel jika belum ada
$tmpStorage = $_ENV['APP_STORAGE'] ?? '/tmp/storage';
if (!is_dir($tmpStorage)) {
    mkdir($tmpStorage . '/framework/cache/data', 0777, true);
    mkdir($tmpStorage . '/framework/sessions', 0777, true);
    mkdir($tmpStorage . '/framework/testing', 0777, true);
    mkdir($tmpStorage . '/framework/views', 0777, true);
    mkdir($tmpStorage . '/logs', 0777, true);
    mkdir($tmpStorage . '/app', 0777, true);
}

// Load public/index.php (Laravel akan otomatis menggunakan $app yang sudah dimodifikasi)
require $publicPath . '/index.php';
