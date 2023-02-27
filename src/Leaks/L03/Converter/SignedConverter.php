<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L03\Converter;

use Morozov\PhpTek2023\Leaks\L03\Converter;

use function ip2long;
use function long2ip;

final readonly class SignedConverter implements Converter
{
    private const OFFSET = 0x80000000;

    public function ip2long(string $ip): int
    {
        return ip2long($ip) - self::OFFSET;
    }

    public function long2ip(int $long): string
    {
        return long2ip($long + self::OFFSET);
    }

    public function toString(): string
    {
        return 'signed';
    }
}
