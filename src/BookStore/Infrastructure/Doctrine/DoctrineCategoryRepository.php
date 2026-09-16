<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Doctrine;

use App\BookStore\Domain\Exception\MissingCategoryException;
use App\BookStore\Domain\Model\Category;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;
use App\Shared\Infrastructure\Doctrine\PaginatesWithDoctrine;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(CategoryRepositoryInterface::class, when: ['dev', 'prod'])]
final readonly class DoctrineCategoryRepository implements CategoryRepositoryInterface
{
    /** @use PaginatesWithDoctrine<Category> */
    use PaginatesWithDoctrine;

    private const string ALIAS = 'category';

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[\Override]
    public function add(Category $category): void
    {
        $this->em->persist($category);
    }

    #[\Override]
    public function get(CategoryId $id): Category
    {
        return $this->em->find(Category::class, $id->value) ?? throw new MissingCategoryException($id);
    }

    #[\Override]
    public function all(?Pagination $pagination = null): PaginatedCollection
    {
        return $this->paginate($this->createQueryBuilder(), $pagination);
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->em->createQueryBuilder()
            ->select(self::ALIAS)
            ->from(Category::class, self::ALIAS);
    }
}
