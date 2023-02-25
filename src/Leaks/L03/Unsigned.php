<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L03;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Table;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Test;

use function printf;
use function sprintf;

use const PHP_EOL;

final readonly class Unsigned implements Test
{
    public function __construct(private string $driver)
    {
    }

    public function toString(): string
    {
        return sprintf('Unsigned integers with %s', $this->driver);
    }

    /** @throws Exception */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection($this->driver);

        $this->setUp($connection);

        $result = $connection->insert('memory', ['address' => 0x8000]);

        printf('Inserted %d row(s)' . PHP_EOL, $result);
    }

    /** @throws Exception */
    private function setUp(Connection $connection): void
    {
        $table = new Table('memory');
        $table->addColumn('address', 'smallint', ['unsigned' => true]);

        $connection->createSchemaManager()
            ->dropAndCreateTable($table);
    }
}
