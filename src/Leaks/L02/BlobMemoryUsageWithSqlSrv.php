<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02;

use Doctrine\DBAL;
use Doctrine\ORM\Exception\ORMException;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Leaks\L02\Driver\SqlSrvDriver;
use Morozov\PhpTek2023\Test;

use function assert;
use function is_resource;

final readonly class BlobMemoryUsageWithSqlSrv implements Test
{
    public function toString(): string
    {
        return 'Blob memory usage with sqlsrv used directly';
    }

    /**
     * @throws DBAL\Exception
     * @throws ORMException
     */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection('sqlsrv');

        $runner = new Runner();

        $entityManager = $runner->createEntityManager($connection);

        $nativeConnection = $connection->getNativeConnection();
        assert(is_resource($nativeConnection));

        $runner->run($entityManager, new SqlSrvDriver($nativeConnection));
    }
}
