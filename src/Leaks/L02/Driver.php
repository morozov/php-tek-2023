<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02;

interface Driver
{
    public function persistMessage(Message $message): int|null;

    public function fetchAttachment(): mixed;
}
