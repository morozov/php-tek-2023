<?php

declare(strict_types=1);

use Morozov\PhpTek2023\Leaks\L02\BlobMemoryUsageWithOrm;
use Morozov\PhpTek2023\Leaks\L02\BlobMemoryUsageWithPdoPgSql;
use Morozov\PhpTek2023\Leaks\L02\BlobMemoryUsageWithPgSql;
use Morozov\PhpTek2023\Runner;

require __DIR__ . '/../vendor/autoload.php';

(static function (): void {
    $tests = [
        new BlobMemoryUsageWithOrm('mysqli'),
        new BlobMemoryUsageWithOrm('pdo_pgsql'),
        new BlobMemoryUsageWithOrm('pdo_mysql'),
        new BlobMemoryUsageWithPdoPgSql(),
        new BlobMemoryUsageWithPgSql(),
        //new BlobMemoryUsageWithSqlSrv(),
    ];

    (new Runner())->run($tests);
})();
