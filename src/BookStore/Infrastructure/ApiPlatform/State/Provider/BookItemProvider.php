<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Application\Query\FindBookQuery;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\Uid\Uuid;

final class BookItemProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    /**
     * @return BookResource|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var string $id */
        $id = $uriVariables['id'];

        /** @var Book|null $model */
        $model = $this->queryBus->ask(new FindBookQuery(new BookId(Uuid::fromString($id))));

        return null !== $model ? BookResource::fromModel($model) : null;
    }
}
