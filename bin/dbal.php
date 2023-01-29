<?php

use Doctrine\DBAL\DriverManager;

require __DIR__ . '/../vendor/autoload.php';

function connect_to_mysqli() {
    return DriverManager::getConnection([
        'driver' => 'mysqli',
        'host'   => '127.0.0.1',
        'user'   => 'root',
        'dbname' => 'mysql',
    ]);
}

function connect_to_oci8() {
    return DriverManager::getConnection([
        'driver'   => 'oci8',
        'host'     => '127.0.0.1',
        'user'     => 'system',
        'password' => 'oracle',
        'dbname'   => 'XE',
    ]);
}

function connect_to_sqlsrv() {
    return DriverManager::getConnection([
        'driver'   => 'sqlsrv',
        'host'     => '127.0.0.1',
        'user'     => 'sa',
        'password' => 'Passw0rd',
    ]);
}

function connect_to_pdo_mysql() {
    return DriverManager::getConnection([
        'driver' => 'pdo_mysql',
        'host'   => '127.0.0.1',
        'user'   => 'root',
        'dbname' => 'mysql',
    ]);
}

function connect_to_pdo_oci() {
    return DriverManager::getConnection([
        'driver'   => 'pdo_oci',
        'host'     => '127.0.0.1',
        'user'     => 'system',
        'password' => 'oracle',
        'dbname'   => 'XE',
    ]);
}

function connect_to_pdo_pgsql() {
    return DriverManager::getConnection([
        'driver'   => 'pdo_pgsql',
        'host'     => '127.0.0.1',
        'user'     => 'postgres',
        'password' => 'Passw0rd',
    ]);
}

function connect_to_pdo_sqlite() {
    return DriverManager::getConnection([
        'driver' => 'pdo_sqlite',
        'path'   => dirname(__DIR__) . '/db.sqlite',
    ]);
}

function connect_to_pdo_sqlsrv() {
    return DriverManager::getConnection([
        'driver'   => 'pdo_sqlsrv',
        'host'     => '127.0.0.1',
        'user'     => 'sa',
        'password' => 'Passw0rd',
    ]);
}

function connect() {
    return connect_to_pdo_oci();
}
