<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\InMemory;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookId;
use App\Shared\Infrastructure\InMemory\InMemoryRepository;

/**
 * @extends InMemoryRepository<Book>
 */
final class InMemoryBookRepository extends InMemoryRepository implements BookRepositoryInterface
{
    public function save(Book $book): void
    {
        $this->entities[(string) $book->id()] = $book;
    }

    public function remove(Book $book): void
    {
        unset($this->entities[(string) $book->id()]);
    }

    public function ofId(BookId $id): ?Book
    {
        return $this->entities[(string) $id] ?? null;
    }

    public function withAuthor(Author $author): static
    {
        return $this->filter(fn (Book $book) => $book->author()->isEqualTo($author));
    }

    public function withCheapestsFirst(): static
    {
        $cloned = clone $this;
        uasort($cloned->entities, fn (Book $a, Book $b) => $a->price() <=> $b->price());

        return $cloned;
    }
}
