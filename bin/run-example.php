#!/usr/bin/env php
<?php

declare(strict_types=1);

use Morozov\PhpTek2023\ConnectionProvider;
use Morozov\PhpTek2023\Runner;

require __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['argc'] < 2) {
    echo 'Usage: ' . $_SERVER['argv'][0] . ' <example> [<driver>]' . PHP_EOL;
    echo 'Available drivers: ' . implode(', ', ConnectionProvider::getAvailableDrivers()) . PHP_EOL;
    exit(1);
}

$runner = new Runner();

try {
    $runner->run($_SERVER['argv']);
} catch (Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
