<?php

use Doctrine\DBAL\Schema\Table;

require __DIR__ . '/../dbal.php';

$table = new Table('orders');
$table->addColumn('id', 'integer');
$table->addColumn('status', 'string', ['length' => 16]);

$conn = connect();
$sm = $conn->createSchemaManager();
$sm->dropAndCreateTable($table);

$conn->insert('orders', ['id' => 1, 'status' => 'pending']);

$conn->createQueryBuilder()
    ->update('orders', 'o')
    ->set('o.status', '?')
    ->where('o.id = ?')
    ->setParameter(0, 'shipped')
    ->setParameter(1, 1)
    ->executeStatement();

// MySQL: OK
// PostgreSQL: Undefined column: column "o" of relation "orders" does not exist
// SQLite: General error: 1 near "o": syntax error
// Furthermore, you can build a query that won't be valid on any platform.