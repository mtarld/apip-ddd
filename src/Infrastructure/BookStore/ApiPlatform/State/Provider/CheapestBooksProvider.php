<?php

declare(strict_types=1);

namespace App\Infrastructure\BookStore\ApiPlatform\State\Provider;

use ApiPlatform\State\ProviderInterface;
use App\Application\BookStore\Query\FindCheapestBooksQuery;
use App\Application\Shared\Query\QueryBusInterface;
use App\Domain\BookStore\Repository\BookRepositoryInterface;
use App\Infrastructure\BookStore\ApiPlatform\Resource\BookResource;
use App\Infrastructure\Shared\ApiPlatform\Metadata\QueryOperation;

final class CheapestBooksProvider implements ProviderInterface
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    /**
     * @return list<BookResource>
     */
    public function provide(string $resourceClass, array $identifiers = [], ?string $operationName = null, array $context = []): object|array|null
    {
        /** @var BookRepositoryInterface $models */
        $models = $this->queryBus->ask(new FindCheapestBooksQuery());

        $resources = [];
        foreach ($models as $model) {
            $resources[] = BookResource::fromModel($model);
        }

        return $resources;
    }

    public function supports(string $resourceClass, array $identifiers = [], ?string $operationName = null, array $context = []): bool
    {
        return $context['operation'] instanceof QueryOperation && FindCheapestBooksQuery::class === $context['operation']->getQuery();
    }
}
