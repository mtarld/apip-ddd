<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Symfony\Audit;

use App\BookStore\Application\Audit\AuditTrail;
use App\BookStore\Domain\ValueObject\Actor;
use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Domain\ValueObject\AuthorId;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;

final readonly class MonologAuditTrail implements AuditTrail
{
    public function __construct(
        #[Target('auditLogger')] private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function recordAnonymization(
        Actor $actor,
        AuthorId $authorId,
        int $books,
        AnonymizationReason $reason,
        \DateTimeImmutable $at,
    ): void {
        $this->logger->info('{books} books anonymized for author "{author}" by {actor}. Reason: {reason}', [
            'books' => $books,
            'author' => (string) $authorId,
            'actor' => (string) $actor,
            'reason' => (string) $reason,
            'at' => $at->format(\DATE_ATOM),
        ]);
    }
}
