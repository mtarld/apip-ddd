<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

final class UpdateBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private readonly BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(UpdateBookCommand $command): Book
    {
        $book = $this->bookRepository->ofId($command->id);
        if (null === $book) {
            throw new MissingBookException($command->id);
        }

        $book->name = $command->name ?? $book->name;
        $book->description = $command->description ?? $book->description;
        $book->author = $command->author ?? $book->author;
        $book->content = $command->content ?? $book->content;
        $book->price = $command->price ?? $book->price;

        $this->bookRepository->save($book);

        return $book;
    }
}
