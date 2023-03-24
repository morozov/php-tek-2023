<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L03\Converter;

use Morozov\PhpTek2023\Leaks\L03\Converter;

final readonly class SignedConverter implements Converter
{
    public function __construct(private Converter $converter)
    {
    }

    public function ip2long(string $ip): int
    {
        return $this->unsigned2signed(
            $this->converter->ip2long($ip),
        );
    }

    public function long2ip(int $long): string
    {
        return $this->converter->long2ip(
            $this->signed2unsigned($long),
        );
    }

    private function unsigned2signed(int $unsigned): int
    {
        if ($unsigned < 0x80000000) {
            return $unsigned;
        }

        return $unsigned - 0x100000000;
    }

    private function signed2unsigned(int $signed): int
    {
        if ($signed >= 0) {
            return $signed;
        }

        return $signed + 0x100000000;
    }

    public function toString(): string
    {
        return 'signed';
    }
}
