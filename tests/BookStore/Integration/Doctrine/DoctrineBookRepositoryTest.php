<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\Doctrine;

use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookRepository;
use App\Shared\Infrastructure\Doctrine\DoctrinePaginator;
use App\Tests\BookStore\DummyFactory\DummyBookFactory;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

final class DoctrineBookRepositoryTest extends KernelTestCase
{
    private static Connection $connection;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::bootKernel();

        static::$connection = static::getContainer()->get(Connection::class);

        (new Application(static::$kernel))
            ->find('doctrine:database:create')
            ->run(new ArrayInput(['--if-not-exists' => true]), new NullOutput());

        (new Application(static::$kernel))
            ->find('doctrine:schema:update')
            ->run(new ArrayInput(['--force' => true]), new NullOutput());
    }

    protected function setUp(): void
    {
        static::bootKernel();
        static::$connection->executeStatement('TRUNCATE book');
    }

    public function testAdd(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);

        static::assertEmpty($repository);

        $book = DummyBookFactory::createBook();
        $repository->add($book);

        static::assertCount(1, $repository);
    }

    public function testRemove(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);

        $book = DummyBookFactory::createBook();
        $repository->add($book);

        static::assertCount(1, $repository);

        $repository->remove($book);
        static::assertEmpty($repository);
    }

    public function testOfId(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);

        static::assertEmpty($repository);

        $book = DummyBookFactory::createBook();
        $repository->add($book);

        static::assertSame($book, $repository->ofId($book->id));
    }

    public function testWithAuthor(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);

        $repository->add(DummyBookFactory::createBook(author: 'authorOne'));
        $repository->add(DummyBookFactory::createBook(author: 'authorOne'));
        $repository->add(DummyBookFactory::createBook(author: 'authorTwo'));

        static::assertCount(2, $repository->withAuthor(new Author('authorOne')));
        static::assertCount(1, $repository->withAuthor(new Author('authorTwo')));
    }

    public function testWithCheapestsFirst(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);

        $repository->add(DummyBookFactory::createBook(price: 1));
        $repository->add(DummyBookFactory::createBook(price: 3));
        $repository->add(DummyBookFactory::createBook(price: 2));

        $prices = [];
        foreach ($repository->withCheapestsFirst() as $book) {
            $prices[] = $book->price->value;
        }
        static::assertSame([1, 2, 3], $prices);
    }

    public function testWithPagination(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);
        static::assertNull($repository->paginator());

        $repository = $repository->withPagination(1, 2);

        static::assertInstanceOf(DoctrinePaginator::class, $repository->paginator());
    }

    public function testWithoutPagination(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);
        $repository = $repository->withPagination(1, 2);
        static::assertNotNull($repository->paginator());

        $repository = $repository->withoutPagination();
        static::assertNull($repository->paginator());
    }

    public function testIteratorWithoutPagination(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);
        static::assertNull($repository->paginator());

        $books = [
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
        ];
        foreach ($books as $book) {
            $repository->add($book);
        }

        $i = 0;
        foreach ($repository as $book) {
            static::assertSame($books[$i], $book);
            ++$i;
        }
    }

    public function testIteratorWithPagination(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);
        static::assertNull($repository->paginator());

        $books = [
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
        ];

        foreach ($books as $book) {
            $repository->add($book);
        }

        $repository = $repository->withPagination(1, 2);

        $i = 0;
        foreach ($repository as $book) {
            static::assertContains($book, $books);
            ++$i;
        }

        static::assertSame(2, $i);

        $repository = $repository->withPagination(2, 2);

        $i = 0;
        foreach ($repository as $book) {
            static::assertContains($book, $books);
            ++$i;
        }

        static::assertSame(1, $i);
    }

    public function testCount(): void
    {
        /** @var DoctrineBookRepository $repository */
        $repository = static::getContainer()->get(DoctrineBookRepository::class);

        $books = [
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
            DummyBookFactory::createBook(),
        ];
        foreach ($books as $book) {
            $repository->add($book);
        }

        static::assertCount(count($books), $repository);
        static::assertCount(2, $repository->withPagination(1, 2));
    }
}
