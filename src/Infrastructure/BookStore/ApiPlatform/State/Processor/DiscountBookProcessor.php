<?php

declare(strict_types=1);

namespace App\Infrastructure\BookStore\ApiPlatform\State\Processor;

use ApiPlatform\State\ProcessorInterface;
use App\Application\BookStore\Command\DiscountBookCommand;
use App\Application\BookStore\Query\FindBookQuery;
use App\Application\Shared\Command\CommandBusInterface;
use App\Application\Shared\Query\QueryBusInterface;
use App\Domain\BookStore\Model\Book;
use App\Infrastructure\BookStore\ApiPlatform\Resource\BookResource;

final class DiscountBookProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {
    }

    public function process($data, array $identifiers = [], ?string $operationName = null, array $context = []): BookResource
    {
        $this->commandBus->dispatch($data);

        /** @var Book $model */
        $model = $this->queryBus->ask(new FindBookQuery($data->id));

        return BookResource::fromModel($model);
    }

    public function supports($data, array $identifiers = [], ?string $operationName = null, array $context = []): bool
    {
        return $data instanceof DiscountBookCommand;
    }
}
