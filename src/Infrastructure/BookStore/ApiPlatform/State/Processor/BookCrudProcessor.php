<?php

declare(strict_types=1);

namespace App\Infrastructure\BookStore\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\BookStore\Command\CreateBookCommand;
use App\Application\BookStore\Command\DeleteBookCommand;
use App\Application\BookStore\Command\UpdateBookCommand;
use App\Application\Shared\Command\CommandBusInterface;
use App\Domain\BookStore\Model\Book;
use App\Infrastructure\BookStore\ApiPlatform\Resource\BookResource;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final class BookCrudProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, BookResource::class);

        if ($operation instanceof DeleteOperationInterface) {
            $this->commandBus->dispatch(new DeleteBookCommand(Uuid::fromString($uriVariables['id'])));

            return null;
        }

        $command = !isset($uriVariables['id'])
            ? new CreateBookCommand($data->name, $data->description, $data->author, $data->content, $data->price)
            : new UpdateBookCommand(Uuid::fromString($uriVariables['id']), $data->name, $data->description, $data->author, $data->content, $data->price)
        ;

        /** @var Book $model */
        $model = $this->commandBus->dispatch($command);

        return BookResource::fromModel($model);
    }
}
