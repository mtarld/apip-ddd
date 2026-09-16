<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\ReviewBookCommand;
use App\BookStore\Domain\Exception\MissingReviewException;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\BookStore\Domain\ValueObject\ReviewRating;
use App\BookStore\Infrastructure\ApiPlatform\Payload\ReviewBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\Resource\ReviewResource;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<ReviewBookPayload, ReviewResource>
 */
final readonly class ReviewBookProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ObjectMapperInterface $objectMapper,
        private BookRepositoryInterface $bookRepository,
    ) {
    }

    #[\Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReviewResource
    {
        Assert::isInstanceOf($data, ReviewBookPayload::class);
        Assert::isInstanceOf($uriVariables['bookId'], BookId::class);

        $id = new ReviewId();

        $this->commandBus->dispatch(new ReviewBookCommand(
            $uriVariables['bookId'],
            $id,
            new ReviewRating($data->rating),
            new ReviewComment($data->comment),
        ));

        $book = $this->bookRepository->get($uriVariables['bookId']);
        foreach ($book->reviews as $review) {
            if ((string) $review->id === (string) $id) {
                return $this->objectMapper->map($review, ReviewResource::class);
            }
        }

        throw new MissingReviewException($uriVariables['bookId'], $id);
    }
}
