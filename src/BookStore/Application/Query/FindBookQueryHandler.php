<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

final class FindBookQueryHandler implements QueryHandlerInterface
{
    public function __construct(private BookRepositoryInterface $repository)
    {
    }

    public function __invoke(FindBookQuery $query): ?Book
    {
        return $this->repository->ofId($query->id);
    }
}
