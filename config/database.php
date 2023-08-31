<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_subsystem' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_SUBSYSTEM', '127.0.0.1'),
            'port' => env('DB_PORT_SUBSYSTEM', '3306'),
            'database' => env('DB_DATABASE_SUBSYSTEM', 'forge'),
            'username' => env('DB_USERNAME_SUBSYSTEM', 'forge'),
            'password' => env('DB_PASSWORD_SUBSYSTEM', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_rapid' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_RAPID', '192.168.3.235'),
            'port' => env('DB_PORT_RAPID', '3306'),
            'database' => env('DB_DATABASE_RAPID', 'forge'),
            'username' => env('DB_USERNAME_RAPID', 'forge'),
            'password' => env('DB_PASSWORD_RAPID', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_rapid_users' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_RAPID_USERS', '192.168.3.235'),
            'port' => env('DB_PORT_RAPID_USERS', '3306'),
            'database' => env('DB_DATABASE_RAPID_USERS', 'forge'),
            'username' => env('DB_USERNAME_RAPID_USERS', 'forge'),
            'password' => env('DB_PASSWORD_RAPID_USERS', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_rapidx' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE_RAPIDX', 'forge'),
            'username' => env('DB_USERNAME_RAPIDX', 'forge'),
            'password' => env('DB_PASSWORD_RAPIDX', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_rapid_cnpts' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_RAPID_CNPTS', '192.168.3.235'),
            'port' => env('DB_PORT_RAPID_CNPTS', '3306'),
            'database' => env('DB_DATABASE_RAPID_CNPTS', 'forge'),
            'username' => env('DB_USERNAME_RAPID_CNPTS', 'forge'),
            'password' => env('DB_PASSWORD_RAPID_CNPTS', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_rapid_dlabel' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_RAPID_DLABEL', '192.168.3.235'),
            'port' => env('DB_PORT_RAPID_DLABEL', '3306'),
            'database' => env('DB_DATABASE_RAPID_DLABEL', 'forge'),
            'username' => env('DB_USERNAME_RAPID_DLABEL', 'forge'),
            'password' => env('DB_PASSWORD_RAPID_DLABEL', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // NO USER SETUP FOR RAPIDX
        'mysql_systemone' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_SYSTEMONE', '127.0.0.1'),
            'port' => env('DB_PORT_SYSTEMONE', '3306'),
            'database' => env('DB_DATABASE_SYSTEMONE', 'forge'),
            'username' => env('DB_USERNAME_SYSTEMONE', 'forge'),
            'password' => env('DB_PASSWORD_SYSTEMONE', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_pmi_cn' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_PMI_CN', '127.0.0.1'),
            'port' => env('DB_PORT_PMI_CN', '3306'),
            'database' => env('DB_DATABASE_PMI_CN', 'forge'),
            'username' => env('DB_USERNAME_PMI_CN', 'forge'),
            'password' => env('DB_PASSWORD_PMI_CN', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_rapid_eedms_v2' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_RAPID_EEDMSV2', '192.168.3.235'),
            'port' => env('DB_PORT_RAPID_EEDMSV2', '3306'),
            'database' => env('DB_DATABASE_RAPID_EEDMSV2', 'forge'),
            'username' => env('DB_USERNAME_RAPID_EEDMSV2', 'forge'),
            'password' => env('DB_PASSWORD_RAPID_EEDMSV2', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // 'mssql_oqcdbm' => [
        //     'driver' => 'sqlsrv',
        //     'url' => env('DATABASE_URL'),
        //     'host' => env('DB_HOST_OQCDBM', '127.0.0.1'),
        //     'port' => env('DB_PORT_OQCDBM', '3306'),
        //     'database' => env('DB_DATABASE_OQCDBM', 'forge'),
        //     'username' => env('DB_USERNAME_OQCDBM', 'forge'),
        //     'password' => env('DB_PASSWORD_OQCDBM', ''),
        //     'unix_socket' => env('DB_SOCKET', ''),
        //     'charset' => 'utf8mb4',
        //     'collation' => 'utf8mb4_unicode_ci',
        //     'prefix' => '',
        //     'prefix_indexes' => true,
        //     'strict' => true,
        //     'engine' => null,
        //     'options' => null,
        // ],

        // 'mssql_oqcdbm' => [
        //     'driver' => 'sqlsrv',
        //     'url' => env('DATABASE_URL'),
        //     'host' => env('DB_HOST_OQCDBM', '127.0.0.1'),
        //     'port' => env('DB_PORT_OQCDBM', '1433'),
        //     'database' => env('DB_DATABASE_OQCDBM', 'forge'),
        //     'username' => env('DB_USERNAME_OQCDBM', 'forge'),
        //     'password' => env('DB_PASSWORD_OQCDBM', ''),
        //     'unix_socket' => env('DB_SOCKET', ''),
        //     'charset' => 'utf8mb4',
        //     'prefix' => '',
        // ],

        'mssql_oqcdbm' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_OQCDBM', '192.168.3.251\MSSQLSERVER'),
            'port' => env('DB_PORT_OQCDBM', '1433'),
            'database' => env('DB_DATABASE_OQCDBM', 'forge'),
            'username' => env('DB_USERNAME_OQCDBM', 'forge'),
            'password' => env('DB_PASSWORD_OQCDBM', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        // 'mysql_subsystem' => [
        //     'driver' => env('DB_CONNECTION_SUBSYSTEM'),
        //     'url' => env('DATABASE_URL'),
        //     'host' => env('DB_HOST'),
        //     'port' => env('DB_PORT'),
        //     'database' => env('DB_DATABASE2', 'forge'),
        //     'username' => env('DB_USERNAME2', 'forge'),
        //     'password' => env('DB_PASSWORD2', ''),
        //     'unix_socket' => env('DB_SOCKET', ''),
        //     'charset' => 'utf8mb4',
        //     'collation' => 'utf8mb4_unicode_ci',
        //     'prefix' => '',
        //     'prefix_indexes' => true,
        //     'strict' => true,
        //     'engine' => null,
        //     'options' => extension_loaded('pdo_mysql') ? array_filter([
        //         PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        //     ]) : [],
        // ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'predis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'predis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

    ],

];
