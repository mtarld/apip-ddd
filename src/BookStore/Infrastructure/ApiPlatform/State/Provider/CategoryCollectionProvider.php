<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination as ApiPlatformPagination;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Domain\Model\Category;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\Shared\Infrastructure\ApiPlatform\State\Paginator;

/**
 * @implements ProviderInterface<Category>
 */
final readonly class CategoryCollectionProvider implements ProviderInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private ApiPlatformPagination $pagination,
    ) {
    }

    /**
     * @return Paginator<Category>
     */
    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Paginator
    {
        $pagination = Paginator::requested($this->pagination, $operation, $context);

        return Paginator::fromCollection($this->categoryRepository->all($pagination));
    }
}
