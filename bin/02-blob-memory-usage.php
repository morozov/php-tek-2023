<?php

declare(strict_types=1);

use Morozov\PhpTek2023\Leaks\L02\BlobMemoryUsageWithDbal;
use Morozov\PhpTek2023\Leaks\L02\BlobMemoryUsageWithOrm;
use Morozov\PhpTek2023\Leaks\L02\BlobMemoryUsageWithPdo;
use Morozov\PhpTek2023\Leaks\L02\BlobMemoryUsageWithPgSql;
use Morozov\PhpTek2023\Leaks\L02\BlobMemoryUsageWithSqlSrv;
use Morozov\PhpTek2023\Runner;

require __DIR__ . '/../vendor/autoload.php';

(static function (): void {
    $tests = [
        // ORM
        new BlobMemoryUsageWithOrm('mysqli'),
        new BlobMemoryUsageWithOrm('pdo_mysql'),
        new BlobMemoryUsageWithOrm('pdo_pgsql'),

        // DBAL
        new BlobMemoryUsageWithDbal('mysqli'),
        new BlobMemoryUsageWithDbal('pdo_mysql'),
        new BlobMemoryUsageWithDbal('pgsql'),
        new BlobMemoryUsageWithDbal('pdo_pgsql'),

        // PDO extensions used directly
        new BlobMemoryUsageWithPdo('pdo_mysql'),
        new BlobMemoryUsageWithPdo('pdo_pgsql'),
        new BlobMemoryUsageWithPdo('pdo_oci'),
        new BlobMemoryUsageWithPdo('pdo_sqlsrv'),

        // Native extensions used directly
        new BlobMemoryUsageWithPgSql(),
        new BlobMemoryUsageWithSqlSrv(),
    ];

    (new Runner())->run($tests);
})();
