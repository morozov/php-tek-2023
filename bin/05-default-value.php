<?php

declare(strict_types=1);

use Morozov\PhpTek2023\Leaks\L05\DefaultValue;
use Morozov\PhpTek2023\Leaks\L05\SqlProvider\CurrentDateSqlProvider;
use Morozov\PhpTek2023\Leaks\L05\SqlProvider\CurrentTimestampSqlProvider;
use Morozov\PhpTek2023\Leaks\L05\SqlProvider\StaticSqlProvider;
use Morozov\PhpTek2023\Runner;

require __DIR__ . '/../vendor/autoload.php';

(new Runner())->run([
    new DefaultValue('pgsql', 'created_at', 'datetime', new CurrentTimestampSqlProvider()),
    new DefaultValue('pgsql', 'created_at', 'date', new CurrentDateSqlProvider()),

    new DefaultValue('mysqli', 'created_at', 'datetime', new CurrentTimestampSqlProvider()),
    new DefaultValue('mysqli', 'created_at', 'date', new CurrentDateSqlProvider()),

    new DefaultValue('mysqli', 'created_at', 'datetime', new StaticSqlProvider('CURRENT_TIMESTAMP')),
    new DefaultValue('mysqli', 'metadata', 'json', new StaticSqlProvider('(JSON_OBJECT())')),
]);
