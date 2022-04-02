<?php

declare(strict_types=1);

namespace App\Application\Library\Query;

use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Domain\Shared\Query\QueryHandlerInterface;

final class FindCheapestBooksQueryHandler implements QueryHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(FindCheapestBooksQuery $query): BookRepositoryInterface
    {
        return $this->bookRepository
            ->withCheapestsFirst()
            ->withPagination(1, $query->size);
    }
}
