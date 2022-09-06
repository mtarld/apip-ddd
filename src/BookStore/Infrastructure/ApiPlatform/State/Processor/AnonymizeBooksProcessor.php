<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\AnonymizeBooksCommand;
use App\Shared\Application\Command\CommandBusInterface;
use Webmozart\Assert\Assert;

final class AnonymizeBooksProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    /**
     * @param mixed $data
     *
     * @return null
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        Assert::isInstanceOf($data, AnonymizeBooksCommand::class);

        $this->commandBus->dispatch($data);

        return null;
    }
}
