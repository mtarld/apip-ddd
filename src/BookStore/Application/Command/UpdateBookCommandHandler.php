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

final class UpdateBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(UpdateBookCommand $command): Book
    {
        $book = $this->bookRepository->ofId($command->id);

        $book->name = null !== $command->name ? new BookName($command->name) : $book->name;
        $book->description = null !== $command->description ? new BookDescription($command->description) : $book->description;
        $book->author = null !== $command->author ? new Author($command->author) : $book->author;
        $book->content = null !== $command->content ? new BookContent($command->content) : $book->content;
        $book->price = null !== $command->price ? new Price($command->price) : $book->price;

        $this->bookRepository->remove($book);
        $this->bookRepository->add($book);

        return $book;
    }
}
