<?php

declare(strict_types=1);

namespace App\Application\Library\Command;

use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Domain\Shared\Command\CommandHandlerInterface;

final class DiscountBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(DiscountBookCommand $command): void
    {
        $book = $this->bookRepository->ofId($command->id);

        $book->price = $book->price - ($book->price * $command->discountPercentage / 100);

        $this->bookRepository->remove($book);
        $this->bookRepository->add($book);
    }
}
