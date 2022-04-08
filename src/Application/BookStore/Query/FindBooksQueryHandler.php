<?php

declare(strict_types=1);

namespace App\Application\BookStore\Query;

use App\Application\Shared\Query\QueryHandlerInterface;
use App\Domain\BookStore\Repository\BookRepositoryInterface;

final class FindBooksQueryHandler implements QueryHandlerInterface
{
    public function __construct(private BookRepositoryInterface $bookRepository)
    {
    }

    public function __invoke(FindBooksQuery $query): BookRepositoryInterface
    {
        $bookRepository = $this->bookRepository;

        if (null !== $query->author) {
            $bookRepository = $bookRepository->withAuthor($query->author);
        }

        if (null !== $query->page && null !== $query->itemsPerPage) {
            $bookRepository = $bookRepository->withPagination($query->page, $query->itemsPerPage);
        }

        return $bookRepository;
    }
}
