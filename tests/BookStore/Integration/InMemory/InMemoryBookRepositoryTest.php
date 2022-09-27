<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\InMemory;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Infrastructure\InMemory\InMemoryBookRepository;
use App\Shared\Infrastructure\InMemory\InMemoryPaginator;
use App\Tests\BookStore\DummyFactory\DummyBookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class InMemoryBookRepositoryTest extends KernelTestCase
{
    public function testAdd(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);

        static::assertEmpty($repository);

        $book = DummyBookFactory::createBook();
        $repository->save($book);

        static::assertCount(1, $repository);
    }

    public function testRemove(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);

        $book = DummyBookFactory::createBook();
        $repository->save($book);

        static::assertCount(1, $repository);

        $repository->remove($book);
        static::assertEmpty($repository);
    }

    public function testOfId(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);

        static::assertEmpty($repository);

        $book = DummyBookFactory::createBook();
        $repository->save($book);

        static::assertSame($book, $repository->ofId($book->id()));
    }

    public function testWithAuthor(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);

        $repository->save(DummyBookFactory::createBook(author: 'authorOne'));
        $repository->save(DummyBookFactory::createBook(author: 'authorOne'));
        $repository->save(DummyBookFactory::createBook(author: 'authorTwo'));

        static::assertCount(2, $repository->withAuthor(new Author('authorOne')));
        static::assertCount(1, $repository->withAuthor(new Author('authorTwo')));
    }

    public function testWithCheapestsFirst(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);

        $repository->save(DummyBookFactory::createBook(price: 1));
        $repository->save(DummyBookFactory::createBook(price: 3));
        $repository->save(DummyBookFactory::createBook(price: 2));

        $prices = [];
        /** @var Book $book */
        foreach ($repository->withCheapestsFirst() as $book) {
            $prices[] = $book->price()->amount;
        }
        static::assertSame([1, 2, 3], $prices);
    }

    public function testWithPagination(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);
        static::assertNull($repository->paginator());

        $repository = $repository->withPagination(1, 2);

        static::assertInstanceOf(InMemoryPaginator::class, $repository->paginator());
    }

    public function testWithoutPagination(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);
        $repository = $repository->withPagination(1, 2);
        static::assertNotNull($repository->paginator());

        $repository = $repository->withoutPagination();
        static::assertNull($repository->paginator());
    }

    public function testIteratorWithoutPagination(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);
        static::assertNull($repository->paginator());

        $books = [
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
        ];
        foreach ($books as $book) {
            $repository->save($book);
        }

        $i = 0;
        foreach ($repository as $book) {
            static::assertSame($books[$i], $book);
            ++$i;
        }
    }

    public function testIteratorWithPagination(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);
        static::assertNull($repository->paginator());

        $books = [
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
        ];
        foreach ($books as $book) {
            $repository->save($book);
        }

        $repository = $repository->withPagination(1, 2);

        $i = 0;
        foreach ($repository as $book) {
            static::assertSame($books[$i], $book);
            ++$i;
        }

        static::assertSame(2, $i);

        $repository = $repository->withPagination(2, 2);

        $i = 0;
        foreach ($repository as $book) {
            static::assertSame($books[$i + 2], $book);
            ++$i;
        }

        static::assertSame(1, $i);
    }

    public function testCount(): void
    {
        /** @var InMemoryBookRepository $repository */
        $repository = static::getContainer()->get(InMemoryBookRepository::class);

        $books = [
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
        ];
        foreach ($books as $book) {
            $repository->save($book);
        }

        static::assertCount(count($books), $repository);
        static::assertCount(2, $repository->withPagination(1, 2));
    }
}
