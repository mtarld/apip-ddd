<?php

declare(strict_types=1);

namespace App\Application\BookStore\Query;

use App\Application\Shared\Query\QueryHandlerInterface;
use App\Domain\BookStore\Repository\BookRepositoryInterface;

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
