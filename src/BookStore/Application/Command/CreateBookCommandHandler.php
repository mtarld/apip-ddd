<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Application\Command\CommandHandlerInterface;

final class CreateBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(CreateBookCommand $command): Book
    {
        $book = new Book(
            new BookName($command->name),
            new BookDescription($command->description),
            new Author($command->author),
            new BookContent($command->content),
            new Price($command->price),
        );

        $this->bookRepository->add($book);

        return $book;
    }
}
