<?php

declare(strict_types=1);

namespace Morozov\PhpTek2023\Examples\E02BlobMemoryUsage;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'messages')]
readonly class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    /** @param resource $attachment */
    public function __construct(
        #[ORM\Column(name: 'attachment', type: 'blob')]
        public mixed $attachment,
    ) {
    }
}