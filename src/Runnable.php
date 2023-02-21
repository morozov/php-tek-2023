<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023;

interface Runnable
{
    public function run(ConnectionProvider $connectionProvider): void;
}
