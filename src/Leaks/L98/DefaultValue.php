<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L98;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Table;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Test;

use function printf;
use function sprintf;

use const PHP_EOL;

final readonly class DefaultValue implements Test
{
    public function __construct(
        private string $driver,
        private string $columnName,
        private string $columnType,
        private SqlProvider $sqlProvider,
    ) {
    }

    public function toString(): string
    {
        return sprintf(
            'Using %s as the default %s column value with %s',
            $this->sqlProvider->toString(),
            $this->columnType,
            $this->driver,
        );
    }

    /** @throws Exception */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection($this->driver);

        $this->setUp($connection);

        $value = $connection->fetchOne('SELECT ' . $this->columnName . ' FROM notes WHERE id = 1');

        printf('Default "%s" value is "%s"' . PHP_EOL, $this->columnName, $value);
    }

    /** @throws Exception */
    private function setUp(Connection $connection): void
    {
        $platform = $connection->getDatabasePlatform();

        $table = new Table('notes');
        $table->addColumn('id', 'integer');
        $table->addColumn($this->columnName, $this->columnType, ['default' => $this->sqlProvider->getSql($platform)]);

        $connection->createSchemaManager()
            ->dropAndCreateTable($table);

        $connection->insert('notes', ['id' => 1]);
    }
}
