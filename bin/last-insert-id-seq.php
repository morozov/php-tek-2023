<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;

require __DIR__ . '/dbal.php';

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

$platform = $conn1->getDatabasePlatform();

$conn1->insert('ai', ['name' => 'test']);
$conn2->insert('ai', ['name' => 'test']);

if (!$platform->supportsIdentityColumns()) {
    $seqName = $platform->getIdentitySequenceName('ai', 'id');
    var_dump($conn1->lastInsertId($seqName));
    var_dump($conn2->lastInsertId($seqName));
} else {
    var_dump($conn1->lastInsertId());
    var_dump($conn2->lastInsertId());
}

// pdo_pgsql, pdo_mysql: works regardless of whether the argument is passed.
// mysqli: ignores the argument
// test on Oracle (Intel, reproducible) and SQL Server (Intel)

// Try this scenario:
// 1. Works everywhere without the argument (Where do we get the sequence name from?)
// 2. Doesn't work on Oracle
// 3. Stops working on SQL Server

// Sequence names:
// PostgreSQL: ai_id_seq
// Oracle: AI_SEQ
// Not passing an argument to oci* results in `false`. On pdo_oci, its:
// Driver does not support this function: driver does not support lastInsertId()
// passing any argument to the (pdo_)sqlsrv drivers results in `false` since there's no backing sequence. W/o the sequence, works fine on both.