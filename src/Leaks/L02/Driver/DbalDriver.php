<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Leaks\L02\Driver;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\BlobType;
use Morozov\PhpTek2023\Leaks\L02\Driver;
use Morozov\PhpTek2023\Leaks\L02\Message;

final readonly class DbalDriver implements Driver
{
    private BlobType $type;

    public function __construct(private Connection $connection)
    {
        $this->type = new BlobType();
    }

    /** @throws Exception */
    public function persistMessage(Message $message): null
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO messages (attachment) VALUES (?)',
        );

        $stmt->bindValue(1, $message->attachment, ParameterType::LARGE_OBJECT);
        $stmt->executeStatement();

        return null;
    }

    /** @throws Exception */
    public function fetchAttachment(): mixed
    {
        return $this->type->convertToPHPValue(
            $this->connection->fetchOne('SELECT attachment FROM messages WHERE id = 1'),
            $this->connection->getDatabasePlatform(),
        );
    }
}
