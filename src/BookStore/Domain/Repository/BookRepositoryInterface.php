<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Repository;

use App\BookStore\Domain\Model\Book;
use App\Shared\Domain\Repository\RepositoryInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends RepositoryInterface<Book>
 */
interface BookRepositoryInterface extends RepositoryInterface
{
    public function add(Book $book): void;

    public function remove(Book $book): void;

    public function ofId(Uuid $id): ?Book;

    public function withAuthor(string $author): static;

    public function withCheapestsFirst(): static;
}
