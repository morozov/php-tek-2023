<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L03;

interface Converter
{
    public function ip2long(string $ip): int;

    public function long2ip(int $long): string;

    public function toString(): string;
}
