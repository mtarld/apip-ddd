<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Event;

use App\Shared\Domain\Event\EventInterface;

trait RecordsEvents
{
    /**
     * @var list<EventInterface>
     */
    private array $recordedEvents = [];

    /**
     * @return list<EventInterface>
     */
    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    private function record(EventInterface $event): void
    {
        $this->recordedEvents[] = $event;
    }
}
