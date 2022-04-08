<?php

declare(strict_types=1);

namespace App\Application\BookStore\Command;

use App\Application\Shared\Command\CommandHandlerInterface;
use App\Domain\BookStore\Repository\BookRepositoryInterface;

final class DeleteBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(DeleteBookCommand $command): void
    {
        if (null === $book = $this->bookRepository->ofId($command->id)) {
            return;
        }

        $this->bookRepository->remove($book);
    }
}
