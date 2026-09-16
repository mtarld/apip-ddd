<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination as ApiPlatformPagination;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Domain\Model\Author;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\Shared\Infrastructure\ApiPlatform\State\Paginator;

/**
 * @implements ProviderInterface<Author>
 */
final readonly class AuthorCollectionProvider implements ProviderInterface
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository,
        private ApiPlatformPagination $pagination,
    ) {
    }

    /**
     * @return Paginator<Author>|list<Author>
     */
    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Paginator|array
    {
        $name = $operation->getParameters()?->get('name')?->getValue();
        if ($name instanceof AuthorName) {
            $author = $this->authorRepository->findByName($name);

            return $author instanceof Author ? [$author] : [];
        }

        $pagination = Paginator::requested($this->pagination, $operation, $context);

        return Paginator::fromCollection($this->authorRepository->all($pagination));
    }
}
