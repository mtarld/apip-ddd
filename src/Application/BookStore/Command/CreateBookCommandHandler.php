<?php

declare(strict_types=1);

namespace App\Application\BookStore\Command;

use App\Application\Shared\Command\CommandHandlerInterface;
use App\Domain\BookStore\Model\Book;
use App\Domain\BookStore\Repository\BookRepositoryInterface;

final class CreateBookCommandHandler implements CommandHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(CreateBookCommand $command): Book
    {
        $book = new Book($command->name, $command->description, $command->author, $command->content, $command->price);

        $this->bookRepository->add($book);

        return $book;
    }
}
