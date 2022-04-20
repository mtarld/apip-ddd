<?php

declare(strict_types=1);

namespace App\Infrastructure\BookStore\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\BookStore\Command\AnonymizeBooksCommand;
use App\Application\Shared\Command\CommandBusInterface;

final class AnonymizeBooksProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->commandBus->dispatch($data);
    }
}
