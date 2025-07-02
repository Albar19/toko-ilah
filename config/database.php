<?php

use Illuminate\Support\Str;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    | Menentukan koneksi database default yang akan digunakan aplikasi.
    | Nilai diambil dari environment variable DB_CONNECTION dengan fallback ke 'mysql'
    */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    | Konfigurasi berbagai jenis database yang didukung Laravel.
    | Semua koneksi database menggunakan PDO, pastikan driver database tersedia.
    */
    'connections' => [

        // Konfigurasi SQLite - Database file-based yang ringan
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),                                    // URL koneksi lengkap (opsional)
            'database' => env('DB_DATABASE', database_path('database.sqlite')), // Path ke file SQLite
            'prefix' => '',                                                  // Prefix untuk nama tabel
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),     // Aktifkan foreign key constraints
        ],

        // Konfigurasi MySQL - Database yang paling umum digunakan
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),                    // URL koneksi lengkap (opsional)
            'host' => env('DB_HOST', '127.0.0.1'),         // Host database (localhost default)
            'port' => env('DB_PORT', '3306'),               // Port MySQL default
            'database' => env('DB_DATABASE', 'forge'),      // Nama database
            'username' => env('DB_USERNAME', 'forge'),      // Username database
            'password' => env('DB_PASSWORD', ''),           // Password database
            'unix_socket' => env('DB_SOCKET', ''),          // Unix socket (Linux/Mac)
            'charset' => 'utf8mb4',                         // Character set untuk emoji support
            'collation' => 'utf8mb4_unicode_ci',           // Collation untuk sorting
            'prefix' => '',                                 // Prefix untuk nama tabel
            'prefix_indexes' => true,                       // Prefix untuk nama index
            'strict' => true,                               // Mode strict SQL
            'engine' => null,                               // Storage engine (InnoDB default)
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'), // SSL certificate
            ]) : [],
        ],

        // Konfigurasi PostgreSQL - Database open source yang powerful
'pgsql' => [
    'driver' => 'pgsql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'railway'),
    'username' => env('DB_USERNAME', 'postgres'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
],

        // Konfigurasi SQL Server - Database Microsoft
        'sqlsrv' => [
            'driver' => 'sqlsrv',                         // Driver SQL Server
            'url' => env('DATABASE_URL'),                 // URL koneksi lengkap (opsional)
            'host' => env('DB_HOST', 'localhost'),       // Host SQL Server
            'port' => env('DB_PORT', '1433'),            // Port SQL Server default
            'database' => env('DB_DATABASE', 'forge'),   // Nama database
            'username' => env('DB_USERNAME', 'forge'),   // Username database
            'password' => env('DB_PASSWORD', ''),        // Password database
            'charset' => 'utf8',                         // Character encoding
            'prefix' => '',                              // Prefix untuk nama tabel
            'prefix_indexes' => true,                    // Prefix untuk nama index
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),     // Enkripsi koneksi (uncomment jika perlu)
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    | Nama tabel yang menyimpan riwayat migration yang sudah dijalankan.
    | Laravel menggunakan ini untuk tracking migration status.
    */
    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    | Konfigurasi Redis untuk caching, session, dan queue.
    | Redis adalah key-value store yang sangat cepat.
    */
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),     // Client Redis yang digunakan

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),   // Cluster configuration
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'), // Key prefix
        ],

        // Koneksi Redis default
        'default' => [
            'url' => env('REDIS_URL'),                    // URL Redis lengkap (opsional)
            'host' => env('REDIS_HOST', '127.0.0.1'),   // Host Redis
            'username' => env('REDIS_USERNAME'),          // Username Redis (Redis 6+)
            'password' => env('REDIS_PASSWORD'),          // Password Redis
            'port' => env('REDIS_PORT', '6379'),         // Port Redis default
            'database' => env('REDIS_DB', '0'),          // Database number Redis
        ],

        // Koneksi Redis untuk cache
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),    // Database terpisah untuk cache
        ],
    ],
];
