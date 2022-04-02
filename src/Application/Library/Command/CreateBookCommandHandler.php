<?php

declare(strict_types=1);

namespace App\Application\Library\Command;

use App\Domain\Library\Model\Book;
use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Domain\Shared\Command\CommandHandlerInterface;

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
