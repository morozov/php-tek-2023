<?php

declare(strict_types=1);

use Morozov\PhpTek2023\Leaks\L04\ReservedKeywords;
use Morozov\PhpTek2023\Runner;

require __DIR__ . '/../vendor/autoload.php';

(new Runner())->run([
    new ReservedKeywords('mysqli'),
    new ReservedKeywords('pgsql'),
]);
