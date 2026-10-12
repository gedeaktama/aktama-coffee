<?php
/**
 * Aktama Coffee - Root Directory Forwarder
 * Memastikan aplikasi langsung berjalan baik saat diakses via root domain
 * maupun subfolder hosting (contoh: https://aktama.com/coffee/).
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Cek Mode Pemeliharaan (Maintenance Mode)
if (file_exists($maintenance = __DIR__ . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Muat Composer Autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

// 3. Jalankan Aplikasi Laravel
if (file_exists(__DIR__ . '/bootstrap/app.php')) {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->handleRequest(Request::capture());
}
