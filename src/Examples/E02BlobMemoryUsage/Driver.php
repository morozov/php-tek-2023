<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Examples\E02BlobMemoryUsage;

interface Driver
{
    public function persistMessage(Message $message): int|null;

    public function fetchAttachment(): mixed;
}
