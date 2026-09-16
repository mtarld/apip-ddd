<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Domain\Model\Category;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Domain\ValueObject\CategoryId;
use Webmozart\Assert\Assert;

/**
 * @implements ProviderInterface<Category>
 */
final readonly class CategoryItemProvider implements ProviderInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Category
    {
        Assert::isInstanceOf($uriVariables['id'], CategoryId::class);

        return $this->categoryRepository->get($uriVariables['id']);
    }
}
