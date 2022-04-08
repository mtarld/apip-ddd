<?php

declare(strict_types=1);

namespace App\Application\BookStore\Command;

use App\Application\Shared\Command\CommandHandlerInterface;
use App\Domain\BookStore\Model\Book;
use App\Domain\BookStore\Repository\BookRepositoryInterface;

final class UpdateBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(UpdateBookCommand $command): Book
    {
        $book = $this->bookRepository->ofId($command->id);

        $book->name = $command->name ?? $book->name;
        $book->description = $command->description ?? $book->description;
        $book->author = $command->author ?? $book->author;
        $book->content = $command->content ?? $book->content;
        $book->price = $command->price ?? $book->price;

        $this->bookRepository->remove($book);
        $this->bookRepository->add($book);

        return $book;
    }
}
