<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L01;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Table;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Test;

use function printf;
use function sprintf;

use const PHP_EOL;

final readonly class LastInsertId implements Test
{
    public function __construct(private string $driver)
    {
    }

    public function toString(): string
    {
        return sprintf('Last insert ID with %s', $this->driver);
    }

    /** @throws Exception */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection1 = $connectionProvider->getConnection($this->driver);
        $connection2 = $connectionProvider->getConnection($this->driver);

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
    private function setUp(Connection $connection): void
    {
        $table = new Table('ai');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 16]);
        $table->setPrimaryKey(['id']);

        $connection->createSchemaManager()
            ->dropAndCreateTable($table);
    }
}
