<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Unit;

use App\BookStore\Application\Audit\AuditTrail;
use App\BookStore\Application\EventListener\RecordAnonymizationListener;
use App\BookStore\Domain\Event\BooksAnonymized;
use App\BookStore\Domain\ValueObject\Actor;
use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Domain\ValueObject\AuthorId;
use PHPUnit\Framework\TestCase;

final class RecordAnonymizationListenerTest extends TestCase
{
    public function testItHandsEveryFieldOfTheEventToTheAuditTrail(): void
    {
        $actor = Actor::system('test');
        $authorId = new AuthorId();
        $reason = new AnonymizationReason('GDPR request');
        $at = new \DateTimeImmutable('2026-03-01 10:00:00');

        $auditTrail = $this->createMock(AuditTrail::class);
        $auditTrail->expects(self::once())
            ->method('recordAnonymization')
            ->with($actor, $authorId, 3, $reason, $at);

        $listener = new RecordAnonymizationListener($auditTrail);
        $listener(new BooksAnonymized($actor, $authorId, 3, $reason, $at));
    }
}
