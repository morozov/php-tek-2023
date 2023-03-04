<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L99;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Table;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Test;

use function printf;
use function sprintf;

use const PHP_EOL;

final readonly class UpdateAlias implements Test
{
    public function __construct(private string $driver)
    {
    }

    public function toString(): string
    {
        return sprintf('Table alias in UPDATE statement with %s', $this->driver);
    }

    /** @throws Exception */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection($this->driver);

        $this->setUp($connection);

        $result = $connection->createQueryBuilder()
            ->update('orders', 'o')
            ->set('o.status', '?')
            ->where('o.id = ?')
            ->setParameter(0, 'shipped')
            ->setParameter(1, 1)
            ->executeStatement();

        printf('Updated %d row(s)' . PHP_EOL, $result);
    }

    /** @throws Exception */
    private function setUp(Connection $connection): void
    {
        $table = new Table('orders');
        $table->addColumn('id', 'integer');
        $table->addColumn('status', 'string', ['length' => 16]);

        $connection->createSchemaManager()
            ->dropAndCreateTable($table);

        $connection->insert('orders', ['id' => 1, 'status' => 'pending']);
    }
}
