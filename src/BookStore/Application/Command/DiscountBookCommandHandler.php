<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

final class DiscountBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private readonly BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(DiscountBookCommand $command): void
    {
        $book = $this->bookRepository->ofId($command->id);
        if (null === $book) {
            throw new MissingBookException($command->id);
        }

        $book->applyDiscount($command->discount);

        $this->bookRepository->save($book);
    }
}
