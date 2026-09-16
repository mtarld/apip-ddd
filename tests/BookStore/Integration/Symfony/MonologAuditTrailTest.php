<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\Symfony;

use App\BookStore\Application\Audit\AuditTrail;
use App\BookStore\Domain\ValueObject\Actor;
use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Domain\ValueObject\AuthorId;
use Monolog\Handler\TestHandler;
use Monolog\LogRecord;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class MonologAuditTrailTest extends KernelTestCase
{
    public function testItWritesWhoDidWhatToWhomAndWhen(): void
    {
        /** @var AuditTrail $auditTrail */
        $auditTrail = self::getContainer()->get(AuditTrail::class);

        $authorId = new AuthorId();
        $auditTrail->recordAnonymization(
            Actor::system('test'),
            $authorId,
            3,
            new AnonymizationReason('GDPR request'),
            new \DateTimeImmutable('2026-03-01 10:00:00'),
        );

        /** @var TestHandler $testHandler */
        $testHandler = self::getContainer()->get('monolog.handler.audit');

        self::assertTrue($testHandler->hasInfoThatContains(\sprintf('3 books anonymized for author "%s" by system:test', $authorId)));
        self::assertTrue($testHandler->hasInfoThatContains('GDPR request'));

        $record = \array_last($testHandler->getRecords());
        self::assertInstanceOf(LogRecord::class, $record);
        self::assertSame('2026-03-01T10:00:00+00:00', $record->context['at']);
    }
}
