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

    if ($_SERVER['argc'] > 1) {
        (new Runner())->run([$tests[$_SERVER['argv'][1]]]);
    } else {
        for ($i = 0, $count = count($tests); $i < $count; $i++) {
            passthru(PHP_BINARY . ' ' . __FILE__ . ' ' . $i);

            if ($i >= $count - 1) {
                continue;
            }

            echo PHP_EOL;
        }
    }
})();
