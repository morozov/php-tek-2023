<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02\Driver;

use Morozov\PhpTek2023\Leaks\L02\Driver;
use Morozov\PhpTek2023\Leaks\L02\Message;
use PDO;

final readonly class PdoDriver implements Driver
{
    public function __construct(private PDO $connection)
    {
    }

    public function persistMessage(Message $message): null
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO messages (attachment) VALUES (?)',
        );

        $stmt->bindValue(1, $message->attachment, PDO::PARAM_LOB);
        $stmt->execute();

        return null;
    }

    public function fetchAttachment(): mixed
    {
        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare(
            'SELECT attachment FROM messages WHERE id = 1',
        );

        $stmt->execute();
        $stmt->bindColumn(1, $attachment, PDO::PARAM_LOB);
        $stmt->fetch();

        return $attachment;
    }
}
