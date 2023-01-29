<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;

require __DIR__ . '/dbal.php';

function connect() {
    return connect_to_pdo_sqlite();
}

function setUp() {
    $table = new Table('ai');
    $table->addColumn('id', 'integer', ['autoincrement' => true]);
    $table->addColumn('name', 'string', ['length' => 16]);
    $table->setPrimaryKey(['id']);

    $conn = connect();
    $sm = $conn->createSchemaManager();
    $sm->dropAndCreateTable($table);
}

setUp();

$conn1 = connect();
$conn2 = connect();

$conn1->insert('ai', ['name' => 'test']);
$conn2->insert('ai', ['name' => 'test']);

var_dump($conn1->lastInsertId('ai_id_seq'));
var_dump($conn2->lastInsertId('ai_id_seq'));

// pdo_pgsql, pdo_mysql: works regardless of whether the argument is passed.
// mysqli: ignores the argument
// test on Oracle (Intel) and SQL Server (Intel)

// Try this scenario:
// 1. Works everywhere without the argument (Where do we get the sequence name from?)
// 2. Doesn't work on Oracle
// 3. Stops working on SQL Server
