<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023;

use Exception;

interface Test
{
    public function toString(): string;

    /** @throws Exception */
    public function run(ConnectionProvider $connectionProvider): void;
}
