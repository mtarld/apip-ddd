<?php

declare(strict_types=1);

namespace App\Infrastructure\Library\ApiPlatform\State\Processor;

use ApiPlatform\State\ProcessorInterface;
use App\Application\Library\Command\AnonymizeBooksCommand;
use App\Domain\Shared\Command\CommandBusInterface;

final class AnonymizeBooksProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process($data, array $identifiers = [], ?string $operationName = null, array $context = []): void
    {
        $this->commandBus->dispatch($data);
    }

    public function supports($data, array $identifiers = [], ?string $operationName = null, array $context = []): bool
    {
        return $data instanceof AnonymizeBooksCommand;
    }
}
