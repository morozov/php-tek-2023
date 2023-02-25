<?php

declare(strict_types=1);

use Morozov\PhpTek2023\Leaks\L01\LastInsertId;
use Morozov\PhpTek2023\Runner;

require __DIR__ . '/../vendor/autoload.php';

(new Runner())->run([
    new LastInsertId('mysqli'),
    new LastInsertId('pdo_pgsql'),
    new LastInsertId('oci8'),
    new LastInsertId('pdo_oci'),
]);
