<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;
use Psr\Clock\ClockInterface;

#[AsCommandHandler]
final readonly class ReviewBookCommandHandler
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(ReviewBookCommand $command): void
    {
        $book = $this->bookRepository->get($command->bookId);

        $book->review($command->id, $command->rating, $command->comment, $this->clock->now());
    }
}
