<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L98;

use Doctrine\DBAL\Platforms\AbstractPlatform;

interface SqlProvider
{
    public function toString(): string;

    public function getSql(AbstractPlatform $platform): string;
}
