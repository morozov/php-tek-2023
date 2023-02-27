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
use function sprintf;

final readonly class BlobMemoryUsageWithPdo implements Test
{
    public function __construct(private string $driver)
    {
    }

    public function toString(): string
    {
        return sprintf('Blob memory usage with %s used directly', $this->driver);
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

        $nativeConnection = $connection->getNativeConnection();
        assert($nativeConnection instanceof PDO);

        $runner->run($entityManager, new PdoDriver($nativeConnection));
    }
}
