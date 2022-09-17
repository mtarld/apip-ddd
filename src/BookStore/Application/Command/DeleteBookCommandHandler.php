<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

final class DeleteBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private readonly BookRepositoryInterface $bookRepository)
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
