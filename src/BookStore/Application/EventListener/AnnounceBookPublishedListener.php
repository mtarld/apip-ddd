<?php

declare(strict_types=1);

namespace App\BookStore\Application\EventListener;

use App\BookStore\Domain\Event\BookPublished;
use App\Shared\Application\Event\AsEventListener;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Event\Integration\BookPublished as BookPublishedIntegrationEvent;

#[AsEventListener]
final readonly class AnnounceBookPublishedListener
{
    public function __construct(
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(BookPublished $event): void
    {
        $this->eventBus->dispatch(new BookPublishedIntegrationEvent(
            (string) $event->bookId,
            $event->name->value,
            (string) $event->authorId,
            $event->price->value,
        ));
    }
}
