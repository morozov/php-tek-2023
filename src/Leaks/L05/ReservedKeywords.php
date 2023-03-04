<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L05;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Table;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Test;

use function printf;
use function sprintf;

use const PHP_EOL;

final readonly class ReservedKeywords implements Test
{
    public function __construct(private string $driver)
    {
    }

    public function toString(): string
    {
        return sprintf('Reserved keywords with %s', $this->driver);
    }

    /** @throws Exception */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection($this->driver);

        $this->setUp($connection);

        $result = $connection->createQueryBuilder()
            ->update('rules')
            ->set('ignore', '?')
            ->where('id = ?')
            ->setParameter(0, true)
            ->setParameter(1, 1)
            ->executeStatement();

        printf('Updated %d row(s)' . PHP_EOL, $result);
    }

    /** @throws Exception */
    private function setUp(Connection $connection): void
    {
        $table = new Table('rules');
        $table->addColumn('id', 'integer');
        $table->addColumn('ignore', 'boolean');

        $connection->createSchemaManager()
            ->dropAndCreateTable($table);

        $connection->insert('rules', ['id' => 1, 'ignore' => false]);
    }
}
