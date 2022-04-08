<?php

declare(strict_types=1);

namespace App\Application\BookStore\Command;

use App\Application\Shared\Command\CommandHandlerInterface;
use App\Domain\BookStore\Repository\BookRepositoryInterface;

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
