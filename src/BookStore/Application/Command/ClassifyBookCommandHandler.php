<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;
use Psr\Clock\ClockInterface;

#[AsCommandHandler]
final readonly class ClassifyBookCommandHandler
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(ClassifyBookCommand $command): void
    {
        $book = $this->bookRepository->get($command->bookId);
        $category = $this->categoryRepository->get($command->categoryId);

        $book->classify($category, $this->clock->now());
    }
}
