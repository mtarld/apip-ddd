<?php

declare(strict_types=1);

namespace App\Infrastructure\Library\ApiPlatform\State\Processor;

use ApiPlatform\State\ProcessorInterface;
use App\Application\Library\Command\DiscountBookCommand;
use App\Application\Library\Query\FindBookQuery;
use App\Domain\Library\Model\Book;
use App\Domain\Shared\Command\CommandBusInterface;
use App\Domain\Shared\Query\QueryBusInterface;
use App\Infrastructure\Library\ApiPlatform\Resource\BookResource;

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
