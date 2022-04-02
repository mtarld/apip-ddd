<?php

declare(strict_types=1);

namespace App\Infrastructure\Library\InMemory;

use App\Domain\Library\Model\Book;
use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Infrastructure\Shared\InMemory\InMemoryRepository;
use Symfony\Component\Uid\Uuid;

final class InMemoryBookRepository extends InMemoryRepository implements BookRepositoryInterface
{
    public function add(Book $book): void
    {
        $this->entities[(string) $book->id] = $book;
    }

    public function remove(Book $book): void
    {
        unset($this->entities[(string) $book->id]);
    }

    public function ofId(Uuid $id): ?Book
    {
        return $this->entities[(string) $id] ?? null;
    }

    public function withAuthor(string $author): static
    {
        return $this->filter(fn (Book $book) => $book->author === $author);
    }

    public function withCheapestsFirst(): static
    {
        $cloned = clone $this;
        usort($cloned->entities, fn (Book $a, Book $b) => $a->price <=> $b->price);

        return $cloned;
    }
}
