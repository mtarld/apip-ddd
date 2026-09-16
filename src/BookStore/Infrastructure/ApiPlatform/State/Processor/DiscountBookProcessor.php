<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\DiscountBookCommand;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Infrastructure\ApiPlatform\Payload\DiscountBookMcpPayload;
use App\BookStore\Infrastructure\ApiPlatform\Payload\DiscountBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<DiscountBookPayload|DiscountBookMcpPayload, BookResource>
 */
final readonly class DiscountBookProcessor implements ProcessorInterface
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
        Assert::isInstanceOfAny($data, [DiscountBookPayload::class, DiscountBookMcpPayload::class]);

        $id = $data instanceof DiscountBookMcpPayload
            ? new BookId(Uuid::fromString($data->id))
            : $uriVariables['id'];
        Assert::isInstanceOf($id, BookId::class);

        $this->commandBus->dispatch(new DiscountBookCommand(
            $id,
            new Discount($data->discountPercentage),
        ));

        return $this->objectMapper->map($this->books->get($id), BookResource::class);
    }
}
