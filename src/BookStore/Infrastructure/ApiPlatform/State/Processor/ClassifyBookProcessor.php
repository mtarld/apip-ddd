<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\ClassifyBookCommand;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\BookStore\Infrastructure\ApiPlatform\Payload\ClassifyBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Infrastructure\ApiPlatform\ResourceLinkResolver;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<ClassifyBookPayload, BookResource>
 */
final readonly class ClassifyBookProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ObjectMapperInterface $objectMapper,
        private BookViewFinder $books,
        private ResourceLinkResolver $links,
    ) {
    }

    #[\Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BookResource
    {
        Assert::isInstanceOf($data, ClassifyBookPayload::class);
        Assert::isInstanceOf($uriVariables['id'], BookId::class);

        $this->commandBus->dispatch(new ClassifyBookCommand(
            $uriVariables['id'],
            new CategoryId($this->links->identity($data, 'category')),
        ));

        return $this->objectMapper->map($this->books->get($uriVariables['id']), BookResource::class);
    }
}
