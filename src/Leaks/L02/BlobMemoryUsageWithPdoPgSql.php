<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02;

use Doctrine\DBAL;
use Doctrine\ORM\Exception\ORMException;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Leaks\L02\Driver\PdoDriver;
use Morozov\PhpTek2023\Test;
use PDO;

use function assert;

final class BlobMemoryUsageWithPdoPgSql implements Test
{
    public function toString(): string
    {
        return 'Blob memory usage with pdo_pgsql used directly';
    }

    /**
     * @throws DBAL\Exception
     * @throws ORMException
     */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection('pdo_pgsql');

        $runner = new Runner();

        $entityManager = $runner->createEntityManager($connection);

        $nativeConnection = $connection->getNativeConnection();
        assert($nativeConnection instanceof PDO);

        $runner->run($entityManager, new PdoDriver($nativeConnection));
    }
}
