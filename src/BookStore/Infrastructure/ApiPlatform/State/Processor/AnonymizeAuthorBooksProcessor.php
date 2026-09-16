<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\AnonymizeAuthorBooksCommand;
use App\BookStore\Domain\ValueObject\Actor;
use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Infrastructure\ApiPlatform\Payload\AnonymizeAuthorBooksPayload;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Infrastructure\ApiPlatform\ResourceLinkResolver;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<AnonymizeAuthorBooksPayload, null>
 */
final readonly class AnonymizeAuthorBooksProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ResourceLinkResolver $links,
    ) {
    }

    #[\Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): null
    {
        Assert::isInstanceOf($data, AnonymizeAuthorBooksPayload::class);

        $this->commandBus->dispatch(new AnonymizeAuthorBooksCommand(
            Actor::system('api'),
            new AuthorId($this->links->identity($data, 'author')),
            new AnonymizationReason($data->reason),
        ));

        return null;
    }
}
