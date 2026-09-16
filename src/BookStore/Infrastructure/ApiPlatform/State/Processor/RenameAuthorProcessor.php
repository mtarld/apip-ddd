<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\RenameAuthorCommand;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\BookStore\Infrastructure\ApiPlatform\Payload\RenameAuthorPayload;
use App\BookStore\Infrastructure\ApiPlatform\Resource\AuthorResource;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<RenameAuthorPayload, AuthorResource>
 */
final readonly class RenameAuthorProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ObjectMapperInterface $objectMapper,
        private AuthorRepositoryInterface $authorRepository,
    ) {
    }

    #[\Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AuthorResource
    {
        Assert::isInstanceOf($data, RenameAuthorPayload::class);
        Assert::isInstanceOf($uriVariables['id'], AuthorId::class);

        $this->commandBus->dispatch(new RenameAuthorCommand(
            $uriVariables['id'],
            new AuthorName($data->name),
        ));

        return $this->objectMapper->map($this->authorRepository->get($uriVariables['id']), AuthorResource::class);
    }
}
