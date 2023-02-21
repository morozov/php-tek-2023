<?php

declare(strict_types=1);

use Doctrine\DBAL\Schema\Table;

require __DIR__ . '/../dbal.php';

$table = new Table('notes');
$table->addColumn('id', 'integer');
//$table->addColumn('created_at', 'datetime', ['default' => '2023-05-18 09:00:00']);
//$table->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
$table->addColumn('metadata', 'json', ['default' => '(JSON_OBJECT())']);

$conn = connect();
$sm   = $conn->createSchemaManager();
$sm->dropAndCreateTable($table);

$conn->insert('events', ['id' => 1]);
var_dump($conn->fetchOne('SELECT created_at FROM events WHERE id = 1'));

// MySQL: BLOB, TEXT, GEOMETRY or JSON column 'metadata' can't have a default value
