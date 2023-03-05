<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L98\SqlProvider;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Morozov\PhpTek2023\Leaks\L98\SqlProvider;

use function sprintf;

final readonly class StaticSqlProvider implements SqlProvider
{
    public function __construct(private string $sql)
    {
    }

    public function toString(): string
    {
        return sprintf('static %s expression', $this->sql);
    }

    public function getSql(AbstractPlatform $platform): string
    {
        return $this->sql;
    }
}
