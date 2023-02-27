<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L03\Converter;

use Morozov\PhpTek2023\Leaks\L03\Converter;

use function ip2long;
use function long2ip;

final readonly class DefaultConverter implements Converter
{
    public function ip2long(string $ip): int
    {
        return ip2long($ip);
    }

    public function long2ip(int $long): string
    {
        return long2ip($long);
    }

    public function toString(): string
    {
        return 'default';
    }
}
