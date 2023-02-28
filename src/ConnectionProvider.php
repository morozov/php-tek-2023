<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use InvalidArgumentException;

use function array_keys;
use function sprintf;

final readonly class ConnectionProvider
{
    private const PARAMETERS = [
        'mysqli' => [
            'host'   => '127.0.0.1',
            'user'   => 'root',
            'dbname' => 'php-tek-2023',
        ],
        'oci8' => [
            'host'     => '127.0.0.1',
            'user'     => 'system',
            'password' => 'oracle',
            'dbname'   => 'XE',
        ],
        'pgsql' => [
            'host'     => '127.0.0.1',
            'user'     => 'postgres',
            'password' => 'Passw0rd',
        ],
        'sqlite3' => [
            'path'   => __DIR__ . '/../db.sqlite',
        ],
        'sqlsrv' => [
            'host'     => '127.0.0.1',
            'user'     => 'sa',
            'password' => 'Passw0rd',
        ],
        'pdo_mysql' => [
            'host'   => '127.0.0.1',
            'user'   => 'root',
            'dbname' => 'php-tek-2023',
        ],
        'pdo_oci' => [
            'host'     => '127.0.0.1',
            'user'     => 'system',
            'password' => 'oracle',
            'dbname'   => 'XE',
        ],
        'pdo_pgsql' => [
            'host'     => '127.0.0.1',
            'user'     => 'postgres',
            'password' => 'Passw0rd',
        ],
        'pdo_sqlite' => [
            'path'   => __DIR__ . '/../db.sqlite',
        ],
        'pdo_sqlsrv' => [
            'host'     => '127.0.0.1',
            'user'     => 'sa',
            'password' => 'Passw0rd',
        ],
    ];

    /** @return list<string> */
    public static function getAvailableDrivers(): array
    {
        return array_keys(self::PARAMETERS);
    }

    /** @throws Exception */
    public function getConnection(string $driver): Connection
    {
        if (! isset(self::PARAMETERS[$driver])) {
            throw new InvalidArgumentException(sprintf('Driver %s is not supported', $driver));
        }

        return DriverManager::getConnection(['driver' => $driver] + self::PARAMETERS[$driver]);
    }
}
