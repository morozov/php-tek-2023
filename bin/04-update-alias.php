<?php

declare(strict_types=1);

use Morozov\PhpTek2023\Leaks\L04\UpdateAlias;
use Morozov\PhpTek2023\Runner;

require __DIR__ . '/../vendor/autoload.php';

(new Runner())->run([
    new UpdateAlias('pdo_mysql'),
    new UpdateAlias('pdo_pgsql'),
]);
