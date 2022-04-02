<?php

declare(strict_types=1);

namespace App\Application\Library\Command;

use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Domain\Shared\Command\CommandHandlerInterface;

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
