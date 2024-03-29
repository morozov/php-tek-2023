<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02;

use Doctrine\DBAL;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Leaks\L02\Driver\Oci8Driver;
use Morozov\PhpTek2023\Test;

use function assert;
use function is_resource;

final readonly class BlobMemoryUsageWithOci8 implements Test
{
    public function toString(): string
    {
        return 'Blob memory usage with oci8 used directly';
    }

    /**
     * @throws DBAL\Exception
     * @throws ORMException
     */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection('oci8');

        $runner = new Runner();

        $entityManager = $runner->createEntityManager($connection);

        $runner->run($entityManager, static function (EntityManager $entityManager): Driver {
            $connection = $entityManager->getConnection()
                ->getNativeConnection();
            assert(is_resource($connection));

            return new Oci8Driver($connection);
        });
    }
}
