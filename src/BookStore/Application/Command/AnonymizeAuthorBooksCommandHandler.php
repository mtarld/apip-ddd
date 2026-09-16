<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Event\BooksAnonymized;
use App\BookStore\Domain\Model\Author;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Application\Event\EventBusInterface;
use Psr\Clock\ClockInterface;

#[AsCommandHandler]
final readonly class AnonymizeAuthorBooksCommandHandler
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private AuthorRepositoryInterface $authorRepository,
        private EventBusInterface $eventBus,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(AnonymizeAuthorBooksCommand $command): void
    {
        $anonymousAuthorId = $this->anonymousAuthor()->id;

        $anonymizedCount = 0;
        foreach ($this->bookRepository->idsByAuthor($command->authorId) as $bookId) {
            $this->bookRepository->get($bookId)->anonymize($anonymousAuthorId);
            ++$anonymizedCount;
        }

        if (0 < $anonymizedCount) {
            $this->eventBus->dispatch(new BooksAnonymized(
                actor: $command->actor,
                authorId: $command->authorId,
                books: $anonymizedCount,
                reason: $command->reason,
                occurredAt: $this->clock->now(),
            ));
        }
    }

    private function anonymousAuthor(): Author
    {
        $anonymous = $this->authorRepository->findByName(AuthorName::anonymous());
        if ($anonymous instanceof Author) {
            return $anonymous;
        }

        $anonymous = new Author(new AuthorId(), AuthorName::anonymous());
        $this->authorRepository->add($anonymous);

        return $anonymous;
    }
}
