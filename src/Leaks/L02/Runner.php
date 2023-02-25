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

use function fclose;
use function fopen;
use function fseek;
use function fwrite;
use function memory_get_peak_usage;
use function printf;
use function random_bytes;
use function sprintf;
use function stream_copy_to_stream;
use function tmpfile;

use const PHP_EOL;

/**
 * We are using the ORM here in order to hide the differences between the type of BLOBs offered by the drivers.
 */
final class Runner
{
    /** @throws MissingMappingDriverImplementation */
    public function createEntityManager(Connection $connection): EntityManager
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__],
            true,
        );

        return new EntityManager($connection, $config);
    }

    /**
     * @throws DBAL\Exception
     * @throws ORMException
     */
    public function run(EntityManager $entityManager, Driver $driver): void
    {
        $this->setUp($entityManager);

        $attachment = $this->createInputStream();
        printf('Stream created.' . PHP_EOL);
        $this->trackAndPrintPeakMemoryUsage();
        echo PHP_EOL;

        $message = new Message($attachment);
        $copied  = $driver->persistMessage($message);

        if ($copied !== null) {
            printf('Copied %s to the database.' . PHP_EOL, $this->formatAsMebibytes($copied));
        } else {
            printf('Copied some bytes to the database.' . PHP_EOL);
        }

        $this->trackAndPrintPeakMemoryUsage();
        echo PHP_EOL;

        fclose($attachment);

        $attachment = $driver->fetchAttachment();

        $copied = $this->copyStreamToDevNull($attachment);

        printf('Copied %s from the database.' . PHP_EOL, $this->formatAsMebibytes($copied));
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

        // Generate and write 20MB of data in 8KB chunks
        for ($i = 0; $i < 2560; $i++) {
            $chunk = random_bytes(8192);
            fwrite($fp, $chunk);
        }

        fseek($fp, 0);

        return $fp;
    }

    /** @param resource $stream */
    private function copyStreamToDevNull(mixed $stream): int
    {
        $output = fopen('/dev/null', 'w');
        $copied = stream_copy_to_stream($stream, $output);
        fclose($output);

        return $copied;
    }

    private function trackAndPrintPeakMemoryUsage(): void
    {
        printf(
            'Peak memory usage: %s.' . PHP_EOL,
            $this->formatAsMebibytes(memory_get_peak_usage()),
        );
    }

    private function formatAsMebibytes(int $value): string
    {
        return sprintf('%d MiB', $value / 1048576);
    }
}
