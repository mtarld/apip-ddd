<?php

declare(strict_types=1);

namespace App\Infrastructure\Library\ApiPlatform\State\Provider;

use ApiPlatform\State\ProviderInterface;
use App\Application\Library\Query\FindCheapestBooksQuery;
use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Domain\Shared\Query\QueryBusInterface;
use App\Infrastructure\Library\ApiPlatform\Resource\BookResource;
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
        return $context['operation'] instanceof QueryOperation && FindCheapestBooksQuery::class === $context['operation']->query;
    }
}
