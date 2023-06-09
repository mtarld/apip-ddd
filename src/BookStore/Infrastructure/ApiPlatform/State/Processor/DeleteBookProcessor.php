<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\DeleteBookCommand;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\Shared\Application\Command\CommandBusInterface;
use Webmozart\Assert\Assert;

final readonly class DeleteBookProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): null
    {
        Assert::isInstanceOf($data, BookResource::class);

        $this->commandBus->dispatch(new DeleteBookCommand(new BookId($data->id)));

        return null;
    }
}
