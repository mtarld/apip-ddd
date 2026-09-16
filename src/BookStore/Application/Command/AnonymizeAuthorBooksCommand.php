<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\Actor;
use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\Shared\Application\Command\CommandInterface;

final readonly class AnonymizeAuthorBooksCommand implements CommandInterface
{
    public function __construct(
        public Actor $actor,
        public AuthorId $authorId,
        public AnonymizationReason $reason,
    ) {
    }
}
