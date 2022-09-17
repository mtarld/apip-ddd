<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Repository;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookId;
use App\Shared\Domain\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<Book>
 */
interface BookRepositoryInterface extends RepositoryInterface
{
    public function save(Book $book): void;

    public function remove(Book $book): void;

    public function ofId(BookId $id): ?Book;

    public function withAuthor(Author $author): static;

    public function withCheapestsFirst(): static;
}
