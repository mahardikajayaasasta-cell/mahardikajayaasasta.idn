<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\KaryawanMiddleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin'    => AdminMiddleware::class,
            'karyawan' => KaryawanMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Fix environment paths for Vercel (Read-Only Filesystem)
if (isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL_URL'])) {
    $tmpStorage = '/tmp/storage';
    if (!is_dir($tmpStorage)) {
        mkdir($tmpStorage . '/framework/cache/data', 0777, true);
        mkdir($tmpStorage . '/framework/sessions', 0777, true);
        mkdir($tmpStorage . '/framework/testing', 0777, true);
        mkdir($tmpStorage . '/framework/views', 0777, true);
        mkdir($tmpStorage . '/logs', 0777, true);
        mkdir($tmpStorage . '/app', 0777, true);
    }
    
    $tmpBootstrap = '/tmp/bootstrap/cache';
    if (!is_dir($tmpBootstrap)) {
        mkdir($tmpBootstrap, 0777, true);
    }
    
    $app->useStoragePath($tmpStorage);
    $app->useBootstrapPath('/tmp/bootstrap');
}

return $app;
