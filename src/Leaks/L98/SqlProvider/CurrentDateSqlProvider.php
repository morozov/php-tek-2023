<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L98\SqlProvider;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Morozov\PhpTek2023\Leaks\L98\SqlProvider;

final readonly class CurrentDateSqlProvider implements SqlProvider
{
    public function toString(): string
    {
        return 'portable current date expression';
    }

    public function getSql(AbstractPlatform $platform): string
    {
        return $platform->getCurrentDateSQL();
    }
}
