<?php

declare(strict_types=1);

namespace App\Application\Library\Command;

use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Domain\Shared\Command\CommandHandlerInterface;

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
