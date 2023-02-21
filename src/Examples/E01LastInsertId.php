<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Examples;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Table;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\RunnableWithDriver;

use function printf;

use const PHP_EOL;

final class E01LastInsertId implements RunnableWithDriver
{
    /** @throws Exception */
    public function run(ConnectionProvider $connectionProvider, string $driver): void
    {
        $connection1 = $connectionProvider->getConnection($driver);
        $connection2 = $connectionProvider->getConnection($driver);

        $this->setUp($connection1);

        $platform = $connection1->getDatabasePlatform();

        $connection1->insert('ai', ['name' => 'test']);
        $connection2->insert('ai', ['name' => 'test']);

        if (! $platform->supportsIdentityColumns()) {
            $seqName = $platform->getIdentitySequenceName('ai', 'id');

            $id1 = $connection1->lastInsertId($seqName);
            $id2 = $connection2->lastInsertId($seqName);
        } else {
            $id1 = $connection1->lastInsertId();
            $id2 = $connection2->lastInsertId();
        }

        printf('ID #1: %d' . PHP_EOL, $id1);
        printf('ID #2: %d' . PHP_EOL, $id2);
    }

    /** @throws Exception */
    public function setUp(Connection $connection): void
    {
        $table = new Table('ai');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 16]);
        $table->setPrimaryKey(['id']);

        $connection->createSchemaManager()
            ->dropAndCreateTable($table);
    }
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
// passing any argument to the (pdo_)sqlsrv drivers results in `false`
// since there's no backing sequence. W/o the sequence, works fine on both.
