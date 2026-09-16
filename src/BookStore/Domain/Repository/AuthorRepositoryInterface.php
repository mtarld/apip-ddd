<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Repository;

use App\BookStore\Domain\Exception\MissingAuthorException;
use App\BookStore\Domain\Model\Author;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;

interface AuthorRepositoryInterface
{
    public function add(Author $author): void;

    /**
     * @throws MissingAuthorException
     */
    public function get(AuthorId $id): Author;

    public function findByName(AuthorName $name): ?Author;

    /**
     * @return PaginatedCollection<Author>
     */
    public function all(?Pagination $pagination = null): PaginatedCollection;
}
