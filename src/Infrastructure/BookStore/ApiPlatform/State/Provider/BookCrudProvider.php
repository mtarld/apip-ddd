<?php

declare(strict_types=1);

namespace App\Infrastructure\BookStore\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Application\BookStore\Query\FindBookQuery;
use App\Application\BookStore\Query\FindBooksQuery;
use App\Application\Shared\Query\QueryBusInterface;
use App\Domain\BookStore\Model\Book;
use App\Domain\BookStore\Repository\BookRepositoryInterface;
use App\Infrastructure\BookStore\ApiPlatform\Resource\BookResource;
use App\Infrastructure\Shared\ApiPlatform\State\Paginator;

final class BookCrudProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private Pagination $pagination,
    ) {
    }

    /**
     * @return BookResource|Paginator<BookResource>|array<BookResource>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$operation instanceof CollectionOperationInterface) {
            /** @var Book|null $model */
            $model = $this->queryBus->ask(new FindBookQuery($uriVariables['id']));

            return null !== $model ? BookResource::fromModel($model) : null;
        }

        $author = $context['filters']['author'] ?? null;
        $offset = $limit = null;

        if ($this->pagination->isEnabled($operation, $context)) {
            $offset = $this->pagination->getPage($context);
            $limit = $this->pagination->getLimit($operation, $context);
        }

        /** @var BookRepositoryInterface $models */
        $models = $this->queryBus->ask(new FindBooksQuery($author, $offset, $limit));

        $resources = [];
        foreach ($models as $model) {
            $resources[] = BookResource::fromModel($model);
        }

        if (null !== $paginator = $models->paginator()) {
            $resources = new Paginator(
                $resources,
                (float) $paginator->getCurrentPage(),
                (float) $paginator->getItemsPerPage(),
                (float) $paginator->getLastPage(),
                (float) $paginator->getTotalItems(),
            );
        }

        return $resources;
    }
}
