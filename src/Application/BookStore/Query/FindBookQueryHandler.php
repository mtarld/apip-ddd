<?php

declare(strict_types=1);

namespace App\Application\BookStore\Query;

use App\Application\Shared\Query\QueryHandlerInterface;
use App\Domain\BookStore\Model\Book;
use App\Domain\BookStore\Repository\BookRepositoryInterface;

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
