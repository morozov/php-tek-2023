<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02;

use Doctrine\DBAL;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Leaks\L02\Driver\PgSqlDriver;
use Morozov\PhpTek2023\Test;
use PgSql\Connection;

use function assert;

final readonly class BlobMemoryUsageWithPgSql implements Test
{
    public function toString(): string
    {
        return 'Blob memory usage with pgsql used directly';
    }

    /**
     * @throws DBAL\Exception
     * @throws ORMException
     */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection('pgsql');

        $runner = new Runner();

        $entityManager = $runner->createEntityManager($connection);

        $runner->run($entityManager, static function (EntityManager $entityManager): Driver {
            $connection = $entityManager->getConnection()
                ->getNativeConnection();
            assert($connection instanceof Connection);

            return new PgSqlDriver($connection);
        });
    }
}
