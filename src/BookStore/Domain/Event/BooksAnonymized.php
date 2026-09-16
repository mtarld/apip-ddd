<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Event;

use App\BookStore\Domain\ValueObject\Actor;
use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\Shared\Domain\Event\EventInterface;

final readonly class BooksAnonymized implements EventInterface
{
    public function __construct(
        public Actor $actor,
        public AuthorId $authorId,
        public int $books,
        public AnonymizationReason $reason,
        public \DateTimeImmutable $occurredAt,
    ) {
    }
}
