<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02;

use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Exception;
use RuntimeException;

use function fclose;
use function feof;
use function fread;
use function fseek;
use function fwrite;
use function memory_get_peak_usage;
use function min;
use function pcntl_fork;
use function pcntl_waitpid;
use function printf;
use function random_bytes;
use function sprintf;
use function strlen;
use function tmpfile;

use const PHP_EOL;

/**
 * We are using the ORM here in order to hide the differences between the type of BLOBs offered by the drivers.
 */
final class Runner
{
    private const BLOB_SIZE = 20971520;

    private int $previousOverhead = 0;

    /** @throws MissingMappingDriverImplementation */
    public function createEntityManager(Connection $connection): EntityManager
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__],
            true,
        );

        return new EntityManager($connection, $config);
    }

    public function run(EntityManager $entityManager, Driver $driver): void
    {
        $this->forkAndWait(function () use ($entityManager, $driver): void {
            $this->write($entityManager, $driver);
        });

        $this->forkAndWait(function () use ($driver): void {
            $this->read($driver);
        });
    }

    private function forkAndWait(callable $process): void
    {
        $pid = pcntl_fork();

        if ($pid === -1) {
            throw new RuntimeException('Failed to fork process');
        }

        if ($pid === 0) {
            $process();
            exit;
        }

        pcntl_waitpid($pid, $status);

        if ($status !== 0) {
            throw new RuntimeException(sprintf('Child process exited with status %d', $status));
        }
    }

    /**
     * @throws DBAL\Exception
     * @throws ORMException
     */
    private function write(EntityManager $entityManager, Driver $driver): void
    {
        printf('Starting the write part.' . PHP_EOL);

        $this->setUp($entityManager);

        $attachment = $this->createInputStream();

        $message = new Message($attachment);
        $copied  = $driver->persistMessage($message);

        if ($copied !== null) {
            printf('Written %s to the database.' . PHP_EOL, $this->formatAsMebibytes($copied));
        } else {
            printf('Written some bytes to the database.' . PHP_EOL);
        }

        $this->trackAndPrintPeakMemoryUsage();
        echo PHP_EOL;

        fclose($attachment);
    }

    private function read(Driver $driver): void
    {
        printf('Starting the read part.' . PHP_EOL);

        $attachment = $driver->fetchAttachment();

        $copied = $this->countStreamBytes($attachment);

        printf('Read %s from the database.' . PHP_EOL, $this->formatAsMebibytes($copied));
        $this->trackAndPrintPeakMemoryUsage();
    }

    /**
     * @throws DBAL\Exception
     * @throws ORMException
     */
    private function setUp(EntityManager $entityManager): void
    {
        $connection = $entityManager->getConnection();

        $connection->executeStatement('DROP TABLE IF EXISTS messages');

        if ($connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $connection->executeStatement('DROP SEQUENCE IF EXISTS messages_id_seq');
        }

        $classes = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($classes);
    }

    /**
     * @return resource
     *
     * @throws Exception
     */
    private function createInputStream(): mixed
    {
        $fp = tmpfile();

        $remaining = self::BLOB_SIZE;

        while ($remaining > 0) {
            $remaining -= fwrite($fp, random_bytes(min($remaining, 8192)));
        }

        fseek($fp, 0);

        return $fp;
    }

    /** @param resource $stream */
    private function countStreamBytes(mixed $stream): int
    {
        $count = 0;

        while (! feof($stream)) {
            $count += strlen(fread($stream, 8192));
        }

        return $count;
    }

    private function trackAndPrintPeakMemoryUsage(): void
    {
        $peakMemoryUsage = memory_get_peak_usage();

        $overhead = (int) ($peakMemoryUsage / self::BLOB_SIZE);

        $increment = $overhead - $this->previousOverhead;

        $this->previousOverhead = $overhead;

        if ($increment > 0) {
            printf(
                'Peak memory usage has increased by %dx the blob size (%s).' . PHP_EOL,
                $increment,
                $this->formatAsMebibytes($peakMemoryUsage),
            );
        } else {
            echo 'Peak memory usage has not increased.' . PHP_EOL;
        }
    }

    private function formatAsMebibytes(int $value): string
    {
        return sprintf('%d MiB', $value / 1048576);
    }
}
