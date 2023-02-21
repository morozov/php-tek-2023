<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Driver;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\Mapping\MappingException;
use Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Driver;
use Morozov\PhpTek2023\Examples\E02BlobMemoryUsage\Message;
use RuntimeException;

use function sprintf;

final readonly class OrmDriver implements Driver
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    /**
     * @throws ORMException
     * @throws MappingException
     */
    public function persistMessage(Message $message): null
    {
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        // make sure we fetch the entities from the database
        $this->entityManager->clear();

        return null;
    }

    /** @throws ORMException */
    public function fetchAttachment(): mixed
    {
        $query = sprintf('SELECT m FROM %s m', Message::class);

        $message = $this->entityManager->createQuery($query)
            ->getSingleResult();

        if ($message === null) {
            throw new RuntimeException('Message not found.');
        }

        return $message->attachment;
    }
}
