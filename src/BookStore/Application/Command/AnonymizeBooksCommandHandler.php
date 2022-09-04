<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

final class AnonymizeBooksCommandHandler implements CommandHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(AnonymizeBooksCommand $command): void
    {
        $books = $this->bookRepository->withoutPagination();

        foreach ($books as $book) {
            $book->author = $command->anonymizedName;

            $this->bookRepository->remove($book);
            $this->bookRepository->add($book);
        }
    }
}
