<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ReadModel;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;

interface BookViewFinder
{
    /**
     * @throws MissingBookException
     */
    public function get(BookId $id): BookView;

    /**
     * @return PaginatedCollection<BookView>
     */
    public function all(?Pagination $pagination = null): PaginatedCollection;

    /**
     * @return PaginatedCollection<BookView>
     */
    public function byAuthor(AuthorId $authorId, ?Pagination $pagination = null): PaginatedCollection;

    /**
     * @return PaginatedCollection<BookView>
     */
    public function cheapest(int $size = 10): PaginatedCollection;
}
