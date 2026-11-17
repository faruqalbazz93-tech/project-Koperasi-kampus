<?php
/**
 * Konfigurasi Database
 */

return [
    'driver' => 'mysql', // 'mysql' atau 'sqlite'
    'mysql' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'koperasi_stmik_ikmi',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    'sqlite' => [
        'path' => STORAGE_PATH . '/database.sqlite'
    ]
];

