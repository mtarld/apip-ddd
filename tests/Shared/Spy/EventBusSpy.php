<?php

declare(strict_types=1);

namespace App\Tests\Shared\Spy;

use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Event\EventInterface;

final class EventBusSpy implements EventBusInterface
{
    /** @var list<EventInterface> */
    private array $events = [];

    public function __construct(
        private readonly EventBusInterface $decorated,
    ) {
    }

    public function dispatch(EventInterface $event): void
    {
        $this->events[] = $event;
        $this->decorated->dispatch($event);
    }

    /**
     * @template T of EventInterface
     *
     * @param class-string<T> $eventClass
     *
     * @return T|null
     */
    public function findDispatched(string $eventClass): ?EventInterface
    {
        foreach ($this->events as $event) {
            if ($event instanceof $eventClass) {
                return $event;
            }
        }

        return null;
    }

    public function reset(): void
    {
        $this->events = [];
    }
}
