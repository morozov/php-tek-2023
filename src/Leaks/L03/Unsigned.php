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
    public function __construct(private string $driver, private Converter $converter)
    {
    }

    public function toString(): string
    {
        return sprintf(
            'Unsigned integers with %s and %s converter',
            $this->driver,
            $this->converter->toString(),
        );
    }

    /** @throws Exception */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection($this->driver);

        $this->setUp($connection);

        $this->insert($connection, 'yahoo.com', '74.6.231.21');
        $this->insert($connection, 'google.com', '142.251.46.238');

        $this->selectAll($connection);
    }

    /** @throws Exception */
    private function setUp(Connection $connection): void
    {
        $table = new Table('hosts');
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('ip', 'integer', ['unsigned' => true]);

        $connection->createSchemaManager()
            ->dropAndCreateTable($table);
    }

    private function insert(Connection $connection, string $name, string $ip): void
    {
        $result = $connection->insert('hosts', [
            'name' => $name,
            'ip' => $this->converter->ip2long($ip),
        ]);

        printf('Inserted %d row(s)' . PHP_EOL, $result);
    }

    private function selectAll(Connection $connection): void
    {
        foreach ($connection->iterateKeyValue('SELECT name, ip FROM hosts') as $name => $ip) {
            printf(
                'Name: %s, IP: %s' . PHP_EOL,
                $name,
                $this->converter->long2ip($ip),
            );
        }
    }
}
