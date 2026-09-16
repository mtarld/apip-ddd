<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;

#[AsCommandHandler]
final readonly class DeleteBookCommandHandler
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(DeleteBookCommand $command): void
    {
        $this->bookRepository->remove($this->bookRepository->get($command->id));
    }
}
