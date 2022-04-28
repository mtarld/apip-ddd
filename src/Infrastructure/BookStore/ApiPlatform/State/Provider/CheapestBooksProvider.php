<?php

declare(strict_types=1);

namespace App\Infrastructure\BookStore\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Application\BookStore\Query\FindCheapestBooksQuery;
use App\Application\Shared\Query\QueryBusInterface;
use App\Domain\BookStore\Repository\BookRepositoryInterface;
use App\Infrastructure\BookStore\ApiPlatform\Resource\BookResource;

final class CheapestBooksProvider implements ProviderInterface
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    /**
     * @return list<BookResource>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var BookRepositoryInterface $models */
        $models = $this->queryBus->ask(new FindCheapestBooksQuery());

        $resources = [];
        foreach ($models as $model) {
            $resources[] = BookResource::fromModel($model);
        }

        return $resources;
    }
}
