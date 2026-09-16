<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;

#[AsCommandHandler]
final readonly class DeclassifyBookCommandHandler
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    public function __invoke(DeclassifyBookCommand $command): void
    {
        $book = $this->bookRepository->get($command->bookId);
        $category = $this->categoryRepository->get($command->categoryId);

        $book->declassify($category);
    }
}
