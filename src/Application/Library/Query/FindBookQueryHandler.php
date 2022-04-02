<?php

declare(strict_types=1);

namespace App\Application\Library\Query;

use App\Domain\Library\Model\Book;
use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Domain\Shared\Query\QueryHandlerInterface;

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
