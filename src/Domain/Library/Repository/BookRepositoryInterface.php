<?php

declare(strict_types=1);

namespace App\Domain\Library\Repository;

use App\Domain\Library\Model\Book;
use App\Domain\Shared\Repository\RepositoryInterface;
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
