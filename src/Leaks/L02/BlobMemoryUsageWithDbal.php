<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02;

use Doctrine\DBAL;
use Doctrine\ORM\Exception\ORMException;
use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Leaks\L02\Driver\DbalDriver;
use Morozov\PhpTek2023\Test;

use function sprintf;

/**
 * We are using the ORM here in order to hide the differences between the type of BLOBs offered by the drivers.
 */
final readonly class BlobMemoryUsageWithDbal implements Test
{
    public function __construct(private string $driver)
    {
    }

    public function toString(): string
    {
        return sprintf('Blob memory usage DBAL and %s', $this->driver);
    }

    /**
     * @throws DBAL\Exception
     * @throws ORMException
     */
    public function run(ConnectionProvider $connectionProvider): void
    {
        $connection = $connectionProvider->getConnection($this->driver);

        $runner = new Runner();

        $entityManager = $runner->createEntityManager($connection);

        $runner->run($entityManager, new DbalDriver($connection));
    }
}
