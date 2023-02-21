<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Examples;

use Doctrine\DBAL;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\Mapping\MappingException;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Driver\PgSqlDriver;
use Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Runner;
use Morozov\PhpTek2023\Runnable;
use PgSql\Connection;

use function assert;

final class E02BlobMemoryUsageWithNativePgSql implements Runnable
{
    /**
     * @throws DBAL\Exception
     * @throws ORMException
     * @throws MappingException
     */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection('pgsql');

        $runner = new Runner();

        $entityManager = $runner->createEntityManager($connection);

        $nativeConnection = $connection->getNativeConnection();
        assert($nativeConnection instanceof Connection);

        $runner->run($entityManager, new PgSqlDriver($nativeConnection));
    }
}
