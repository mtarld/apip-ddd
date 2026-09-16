<?php

declare(strict_types=1);

namespace App\BookStore\Application\Audit;

use App\BookStore\Domain\ValueObject\Actor;
use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Domain\ValueObject\AuthorId;

interface AuditTrail
{
    public function recordAnonymization(
        Actor $actor,
        AuthorId $authorId,
        int $books,
        AnonymizationReason $reason,
        \DateTimeImmutable $at,
    ): void;
}
