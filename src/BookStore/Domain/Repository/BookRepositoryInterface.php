<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Repository;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;

interface BookRepositoryInterface
{
    public function add(Book $book): void;

    public function remove(Book $book): void;

    /**
     * @throws MissingBookException
     */
    public function get(BookId $id): Book;

    /**
     * @return iterable<BookId>
     */
    public function idsByAuthor(AuthorId $authorId): iterable;
}
