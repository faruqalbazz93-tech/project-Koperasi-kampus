<?php
/**
 * Konfigurasi Global Aplikasi Koperasi Kampus STMIK IKMI
 */

define('APP_NAME', 'Sistem Koperasi Kampus STMIK IKMI');
define('INSTITUTION_NAME', 'Koperasi Kampus STMIK IKMI');
define('APP_VERSION', '1.0.0');

// User Roles yang didukung
define('ROLE_TAMU', 'Tamu');
define('ROLE_MAHASISWA', 'Mahasiswa');
define('ROLE_DOSEN', 'Dosen');
define('ROLE_STAFF', 'Staff');
define('ROLE_ADMIN', 'Administrator');

// Order Status
define('ORDER_PENDING', 'Pending');
define('ORDER_LUNAS', 'Lunas');
define('ORDER_SELESAI', 'Selesai');

// Deteksi Base URL secara dinamis
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptDir, '/');

// Jika dijalankan dari folder root project pada PHP built-in server atau mod_rewrite
define('BASE_URL', $baseUrl);

// Path Konstanta
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('STORAGE_PATH', ROOT_PATH . '/storage');

