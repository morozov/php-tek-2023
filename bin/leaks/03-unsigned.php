<?php

use Doctrine\DBAL\Schema\Table;

require __DIR__ . '/../dbal.php';

$table = new Table('memory');
$table->addColumn('address', 'smallint', ['unsigned' => true]);

$conn = connect();
$sm = $conn->createSchemaManager();
$sm->dropAndCreateTable($table);

$conn->insert('memory', ['address' => 0x8000]);

// MySQL: OK
// PostgreSQL: Numeric value out of range: value "32768" is out of range for type smallint