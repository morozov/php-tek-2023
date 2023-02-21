<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023;

interface RunnableWithDriver
{
    public function run(ConnectionProvider $connectionProvider, string $driver): void;
}
