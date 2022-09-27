<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Query\FindBooksQuery;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Author;
use App\Shared\Application\Query\QueryBusInterface;
use App\Tests\BookStore\DummyFactory\DummyBookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FindBooksTest extends KernelTestCase
{
    public function testFindBooks(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        /** @var QueryBusInterface $queryBus */
        $queryBus = static::getContainer()->get(QueryBusInterface::class);

        $initialBooks = [
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
        ];

        foreach ($initialBooks as $book) {
            $bookRepository->save($book);
        }

        $books = $queryBus->ask(new FindBooksQuery());

        static::assertCount(count($initialBooks), $books);
        foreach ($books as $book) {
            static::assertContains($book, $initialBooks);
        }
    }

    public function testFilterBooksByAuthor(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        /** @var QueryBusInterface $queryBus */
        $queryBus = static::getContainer()->get(QueryBusInterface::class);

        $bookRepository->save(DummyBookFactory::createBook(author: 'authorOne'));
        $bookRepository->save(DummyBookFactory::createBook(author: 'authorOne'));
        $bookRepository->save(DummyBookFactory::createBook(author: 'authorTwo'));

        static::assertCount(3, $bookRepository);

        /** @var Book[] $books */
        $books = $queryBus->ask(new FindBooksQuery(author: new Author('authorOne')));

        static::assertCount(2, $books);
        foreach ($books as $book) {
            static::assertEquals(new Author('authorOne'), $book->author());
        }
    }

    public function testReturnPaginatedBooks(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        /** @var QueryBusInterface $queryBus */
        $queryBus = static::getContainer()->get(QueryBusInterface::class);

        $initialBooks = [
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
        ];

        foreach ($initialBooks as $book) {
            $bookRepository->save($book);
        }

        $books = $queryBus->ask(new FindBooksQuery(page: 2, itemsPerPage: 2));

        static::assertCount(2, $books);
        $i = 0;
        foreach ($books as $book) {
            static::assertSame($initialBooks[$i + 2], $book);
            ++$i;
        }
    }
}
