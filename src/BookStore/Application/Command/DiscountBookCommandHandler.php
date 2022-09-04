<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Application\Command\CommandHandlerInterface;

final class DiscountBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(DiscountBookCommand $command): void
    {
        $book = $this->bookRepository->ofId($command->id);

        $amount = $book->price->value - ($book->price->value * $command->discountPercentage / 100);
        $book->price = new Price($amount);

        $this->bookRepository->remove($book);
        $this->bookRepository->add($book);
    }
}
