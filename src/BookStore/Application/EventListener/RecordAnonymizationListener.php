<?php

declare(strict_types=1);

namespace App\BookStore\Application\EventListener;

use App\BookStore\Application\Audit\AuditTrail;
use App\BookStore\Domain\Event\BooksAnonymized;
use App\Shared\Application\Event\AsEventListener;

#[AsEventListener]
final readonly class RecordAnonymizationListener
{
    public function __construct(
        private AuditTrail $auditTrail,
    ) {
    }

    public function __invoke(BooksAnonymized $event): void
    {
        $this->auditTrail->recordAnonymization(
            actor: $event->actor,
            authorId: $event->authorId,
            books: $event->books,
            reason: $event->reason,
            at: $event->occurredAt,
        );
    }
}
