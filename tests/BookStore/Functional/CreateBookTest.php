<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\CreateBookCommand;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CreateBookTest extends KernelTestCase
{
    public function testCreateBook(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = static::getContainer()->get(CommandBusInterface::class);

        static::assertEmpty($bookRepository);

        $commandBus->dispatch(new CreateBookCommand(
            new BookName('name'),
            new BookDescription('description'),
            new Author('author'),
            new BookContent('content'),
            new Price(1000),
        ));

        static::assertCount(1, $bookRepository);

        /** @var Book $book */
        $book = array_values(iterator_to_array($bookRepository))[0];

        static::assertEquals(new BookName('name'), $book->name());
        static::assertEquals(new BookDescription('description'), $book->description());
        static::assertEquals(new Author('author'), $book->author());
        static::assertEquals(new BookContent('content'), $book->content());
        static::assertEquals(new Price(1000), $book->price());
    }
}
