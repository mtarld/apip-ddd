<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Application\Event\EventBusInterface;

#[AsCommandHandler]
final readonly class CreateBookCommandHandler
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateBookCommand $command): void
    {
        $book = new Book(
            $command->id,
            $command->isbn,
            $command->name,
            $command->description,
            $command->authorId,
            $command->content,
            $command->price,
        );

        $this->bookRepository->add($book);

        foreach ($book->releaseEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
