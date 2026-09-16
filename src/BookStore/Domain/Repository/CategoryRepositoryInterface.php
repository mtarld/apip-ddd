<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Repository;

use App\BookStore\Domain\Exception\MissingCategoryException;
use App\BookStore\Domain\Model\Category;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;

interface CategoryRepositoryInterface
{
    public function add(Category $category): void;

    /**
     * @throws MissingCategoryException
     */
    public function get(CategoryId $id): Category;

    /**
     * @return PaginatedCollection<Category>
     */
    public function all(?Pagination $pagination = null): PaginatedCollection;
}
