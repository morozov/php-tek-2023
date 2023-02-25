<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02\Driver;

use Morozov\PhpTek2023\Leaks\L02\Driver;
use Morozov\PhpTek2023\Leaks\L02\Message;
use PDO;

use function tmpfile;

final readonly class PdoPgSqlDriver implements Driver
{
    public function __construct(private PDO $connection)
    {
    }

    public function persistMessage(Message $message): null
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO messages (id, attachment) VALUES (nextval(\'messages_id_seq\'), ?)',
        );

        $stmt->bindValue(1, $message->attachment, PDO::PARAM_LOB);
        $stmt->execute();

        return null;
    }

    public function fetchAttachment(): mixed
    {
        return tmpfile();
    }
}
