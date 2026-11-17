<?php
/**
 * Single Entry Point
 * Sistem Koperasi Kampus STMIK IKMI
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Muat Konfigurasi
require_once dirname(__DIR__) . '/app/config/config.php';

// 2. Autoloader Sederhana untuk MVC Core, Services, & Models
spl_autoload_register(function ($class) {
    $directories = [
        APP_PATH . '/core/',
        APP_PATH . '/models/',
        APP_PATH . '/services/',
        APP_PATH . '/controllers/'
    ];

    foreach ($directories as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
 
});

// 3. Jalankan Aplikasi
new App();

