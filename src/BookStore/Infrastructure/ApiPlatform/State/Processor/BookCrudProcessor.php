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
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
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
            $this->commandBus->dispatch(new DeleteBookCommand(new BookId($data->id)));

            return null;
        }

        $command = !isset($uriVariables['id'])
            ? new CreateBookCommand(
                new BookName($data->name),
                new BookDescription($data->description),
                new Author($data->author),
                new BookContent($data->content),
                new Price($data->price),
            )
            : new UpdateBookCommand(
                new BookId($data->id),
                new BookName($data->name),
                new BookDescription($data->description),
                new Author($data->author),
                new BookContent($data->content),
                new Price($data->price),
            )
        ;

        /** @var Book $model */
        $model = $this->commandBus->dispatch($command);

        return BookResource::fromModel($model);
    }
}
