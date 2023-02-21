<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Examples;

use Doctrine\DBAL;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\Mapping\MappingException;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Driver\OrmDriver;
use Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Runner;
use Morozov\PhpTek2023\RunnableWithDriver;

/**
 * We are using the ORM here in order to hide the differences between the type of BLOBs offered by the drivers.
 */
final class E02BlobMemoryUsageWithOrm implements RunnableWithDriver
{
    /**
     * @throws DBAL\Exception
     * @throws ORMException
     * @throws MappingException
     */
    public function run(ConnectionProvider $connectionProvider, string $driver): void
    {
        $connection = $connectionProvider->getConnection($driver);

        $runner = new Runner();

        $entityManager = $runner->createEntityManager($connection);

        $runner->run($entityManager, new OrmDriver($entityManager));
    }
}
