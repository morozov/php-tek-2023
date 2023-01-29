<?php

use Doctrine\DBAL\Schema\Table;

require __DIR__ . '/../dbal.php';

$table = new Table('rules');
$table->addColumn('id', 'integer');
$table->addColumn('ignore', 'boolean');

$conn = connect();
$sm = $conn->createSchemaManager();
$sm->dropAndCreateTable($table);

$conn->insert('rules', ['id' => 1, 'ignore' => false]);

$conn->createQueryBuilder()
    ->update('rules')
    ->set('ignore', '?')
    ->where('id = ?')
    ->setParameter(0, true)
    ->setParameter(1, 1)
    ->executeStatement();

// MySQL: You have an error in your SQL syntax ... near 'ignore) VALUES (?, ?)'
// Oh, apparently, IGNORE is a reserved keyword in MySQL.
// Also, the parameters of `addColumn()`, `insert()` and `set()` and have different semantics.
// The first two accept a column name, so the DBAL quotes it but the third accepts an SQL expression
// (you have to quote identifiers there).
// There's the whole other story about supporting keywords in the DBAL. For now,
// just don't use them or test your code extensively or prepend custom columns
// with some prefix or suffix to avoid collisions.
