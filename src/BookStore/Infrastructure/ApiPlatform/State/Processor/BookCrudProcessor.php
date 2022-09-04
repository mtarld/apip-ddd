<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\CreateBookCommand;
use App\BookStore\Application\Command\DeleteBookCommand;
use App\BookStore\Application\Command\UpdateBookCommand;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\Shared\Application\Command\CommandBusInterface;
use Webmozart\Assert\Assert;

final class BookCrudProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        Assert::isInstanceOf($data, BookResource::class);

        if ($operation instanceof DeleteOperationInterface) {
            $this->commandBus->dispatch(new DeleteBookCommand($data->id));

            return null;
        }

        $command = !isset($uriVariables['id'])
            ? new CreateBookCommand($data->name, $data->description, $data->author, $data->content, $data->price)
            : new UpdateBookCommand($data->id, $data->name, $data->description, $data->author, $data->content, $data->price)
        ;

        /** @var Book $model */
        $model = $this->commandBus->dispatch($command);

        return BookResource::fromModel($model);
    }
}
