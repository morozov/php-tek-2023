<?php

declare(strict_types=1);

use Morozov\PhpTek2023\Leaks\L03\Unsigned;
use Morozov\PhpTek2023\Runner;

require __DIR__ . '/../vendor/autoload.php';

(new Runner())->run([
    new Unsigned('pdo_mysql'),
    new Unsigned('pdo_pgsql'),
]);
