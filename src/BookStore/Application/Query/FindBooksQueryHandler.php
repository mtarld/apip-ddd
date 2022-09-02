<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

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
