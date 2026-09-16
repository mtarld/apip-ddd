<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\CreateBookCommand;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Isbn;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\ApiPlatform\Payload\CreateBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Infrastructure\ApiPlatform\ResourceLinkResolver;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<CreateBookPayload, BookResource>
 */
final readonly class CreateBookProcessor implements ProcessorInterface
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
        Assert::isInstanceOf($data, CreateBookPayload::class);

        $id = new BookId();

        $this->commandBus->dispatch(new CreateBookCommand(
            $id,
            new Isbn($data->isbn),
            new BookName($data->name),
            new BookDescription($data->description),
            new AuthorId($this->links->identity($data, 'author')),
            new BookContent($data->content),
            new Price($data->price),
        ));

        return $this->objectMapper->map($this->books->get($id), BookResource::class);
    }
}
