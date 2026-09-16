<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;

#[AsCommandHandler]
final readonly class DiscountBookCommandHandler
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(DiscountBookCommand $command): void
    {
        $book = $this->bookRepository->get($command->id);

        $book->applyDiscount($command->discount);
    }
}
