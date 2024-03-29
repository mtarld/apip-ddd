<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Query\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindCheapestBooksQueryHandler
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
