<?php

// Vercel PHP Runtime entry point untuk Laravel
$url = $_SERVER['REQUEST_URI'] ?? '/';

// PENTING: Jika path adalah /setup-db, kita GANTI session driver sementara menjadi array
// agar Laravel tidak crash karena mencari table 'sessions' yang belum ada.
if (strpos($url, '/setup-db') !== false) {
    putenv('SESSION_DRIVER=array');
    $_ENV['SESSION_DRIVER'] = 'array';
    $_SERVER['SESSION_DRIVER'] = 'array';
}

$publicPath = __DIR__ . '/../public';
$filePath = $publicPath . parse_url($url, PHP_URL_PATH);

if (is_file($filePath)) {
    return false;
}

// Fix environment untuk Vercel
$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_PORT'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 443 : 80;

// Load public/index.php directly
require $publicPath . '/index.php';
