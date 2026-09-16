<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Application\Command\AsCommandHandler;

#[AsCommandHandler]
final readonly class AmendBookCommandHandler
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(AmendBookCommand $command): void
    {
        $book = $this->bookRepository->get($command->id);

        if ($command->name instanceof BookName) {
            $book->rename($command->name);
        }

        if ($command->description instanceof BookDescription) {
            $book->describe($command->description);
        }

        if ($command->content instanceof BookContent) {
            $book->reviseContent($command->content);
        }

        if ($command->price instanceof Price) {
            $book->reprice($command->price);
        }
    }
}
