<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\InMemory;

use App\BookStore\Domain\Exception\MissingCategoryException;
use App\BookStore\Domain\Model\Category;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;
use App\Shared\Infrastructure\InMemory\PaginatesInMemory;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsAlias(CategoryRepositoryInterface::class, public: true, when: ['test'])]
#[When('test')]
final class InMemoryCategoryRepository implements CategoryRepositoryInterface
{
    use PaginatesInMemory;

    /** @var array<string, Category> */
    private array $categories = [];

    #[\Override]
    public function add(Category $category): void
    {
        $this->categories[(string) $category->id] = $category;
    }

    #[\Override]
    public function get(CategoryId $id): Category
    {
        return $this->categories[(string) $id] ?? throw new MissingCategoryException($id);
    }

    #[\Override]
    public function all(?Pagination $pagination = null): PaginatedCollection
    {
        return $this->paginate($this->categories, $pagination);
    }
}
