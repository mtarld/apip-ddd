<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;
use App\Tests\BookStore\Factory\BookFactory;

abstract class BookRepositoryTestCase extends RepositoryTestCase
{
    public function testAdd(): void
    {
        $repository = $this->repository();

        $book = BookFactory::create();
        $repository->add($book);
        $this->persist();

        self::assertSame((string) $book->id, (string) $repository->get($book->id)->id);
    }

    public function testRemove(): void
    {
        $repository = $this->repository();

        $book = BookFactory::create();
        $repository->add($book);
        $this->persist();

        $repository->remove($book);
        $this->persist();

        $this->expectException(MissingBookException::class);
        $repository->get($book->id);
    }

    public function testGet(): void
    {
        $repository = $this->repository();

        $book = BookFactory::create();
        $repository->add($book);
        $this->persist();
        $this->detach();

        $found = $repository->get($book->id);

        self::assertSame((string) $book->id, (string) $found->id);
        self::assertEquals($book->isbn, $found->isbn);
        self::assertEquals($book->name, $found->name);
        self::assertEquals($book->description, $found->description);
        self::assertSame((string) $book->authorId, (string) $found->authorId);
        self::assertEquals($book->content, $found->content);
        self::assertEquals($book->price, $found->price);
    }

    public function testGetRefusesAnUnknownIdentifier(): void
    {
        $this->expectException(MissingBookException::class);

        $this->repository()->get(new BookId());
    }

    public function testIdsByAuthorStreamsIdentifiers(): void
    {
        $target = new AuthorId();
        $other = new AuthorId();

        $repository = $this->repository();

        $expected = [];
        for ($i = 0; $i < 3; ++$i) {
            $book = BookFactory::create(authorId: $target);
            $repository->add($book);
            $expected[] = (string) $book->id;
        }
        $repository->add(BookFactory::create(authorId: $other));
        $this->persist();
        $this->detach();

        $actual = [];
        foreach ($repository->idsByAuthor($target) as $id) {
            self::assertInstanceOf(BookId::class, $id);
            $actual[] = (string) $id;
        }

        \sort($expected);
        \sort($actual);
        self::assertSame($expected, $actual);
    }

    abstract protected function repository(): BookRepositoryInterface;
}
