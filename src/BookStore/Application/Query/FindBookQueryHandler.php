<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Query\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindBookQueryHandler
{
    public function __construct(private BookRepositoryInterface $repository)
    {
    }

    public function __invoke(FindBookQuery $query): Book
    {
        $book = $this->repository->ofId($query->id);
        if (null === $book) {
            throw new MissingBookException($query->id);
        }

        return $book;
    }
}
