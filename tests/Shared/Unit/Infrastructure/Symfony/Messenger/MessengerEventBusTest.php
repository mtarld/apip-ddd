<?php

declare(strict_types=1);

namespace App\Tests\Shared\Unit\Infrastructure\Symfony\Messenger;

use App\Shared\Domain\Event\Integration\BookPublished;
use App\Shared\Infrastructure\Symfony\Messenger\MessengerEventBus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class MessengerEventBusTest extends TestCase
{
    public function testEventsAreHeldBackUntilTheCurrentHandlerHasCommitted(): void
    {
        $bus = new class implements MessageBusInterface {
            public ?Envelope $dispatched = null;

            public function dispatch(object $message, array $stamps = []): Envelope
            {
                return $this->dispatched = Envelope::wrap($message, $stamps);
            }
        };

        $event = new BookPublished('a-book', 'The Dispossessed', 'an-author', 1400);

        new MessengerEventBus($bus)->dispatch($event);

        self::assertNotNull($bus->dispatched);
        self::assertSame($event, $bus->dispatched->getMessage());
        self::assertNotNull($bus->dispatched->last(DispatchAfterCurrentBusStamp::class));
    }
}
