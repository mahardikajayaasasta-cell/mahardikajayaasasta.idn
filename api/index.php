<?php

// Vercel PHP Runtime entry point untuk Laravel
// File ini mengarahkan semua request ke Laravel's public/index.php

$url = $_SERVER['REQUEST_URI'] ?? '/';

// Handle static assets
$publicPath = __DIR__ . '/../public';
$filePath = $publicPath . parse_url($url, PHP_URL_PATH);

if (is_file($filePath)) {
    return false;
}

// Load Laravel
define('LARAVEL_START', microtime(true));

// Fix untuk Vercel deployment
$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_PORT'] = isset($_SERVER['HTTPS']) ? 443 : 80;
$_SERVER['HTTPS'] = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 
                    $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'on' : null;

require $publicPath . '/index.php';
