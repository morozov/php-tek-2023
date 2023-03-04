<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L04\SqlProvider;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Morozov\PhpTek2023\Leaks\L04\SqlProvider;

final readonly class CurrentTimestampSqlProvider implements SqlProvider
{
    public function toString(): string
    {
        return 'portable current timestamp expression';
    }

    public function getSql(AbstractPlatform $platform): string
    {
        return $platform->getCurrentTimestampSQL();
    }
}
