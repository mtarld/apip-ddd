<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\AmendBookCommand;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\ApiPlatform\Payload\AmendBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<AmendBookPayload, BookResource>
 */
final readonly class AmendBookProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ObjectMapperInterface $objectMapper,
        private BookViewFinder $books,
    ) {
    }

    #[\Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BookResource
    {
        Assert::isInstanceOf($data, AmendBookPayload::class);
        Assert::isInstanceOf($uriVariables['id'], BookId::class);

        $this->commandBus->dispatch(new AmendBookCommand(
            $uriVariables['id'],
            null !== $data->name ? new BookName($data->name) : null,
            null !== $data->description ? new BookDescription($data->description) : null,
            null !== $data->content ? new BookContent($data->content) : null,
            null !== $data->price ? new Price($data->price) : null,
        ));

        return $this->objectMapper->map($this->books->get($uriVariables['id']), BookResource::class);
    }
}
