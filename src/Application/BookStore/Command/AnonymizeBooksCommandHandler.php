<?php

declare(strict_types=1);

namespace App\Application\BookStore\Command;

use App\Application\Shared\Command\CommandHandlerInterface;
use App\Domain\BookStore\Repository\BookRepositoryInterface;

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
