<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Domain\Exception\MissingReviewException;
use App\BookStore\Domain\Model\Review;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\ReviewId;
use Webmozart\Assert\Assert;

/**
 * @implements ProviderInterface<Review>
 */
final readonly class BookReviewItemProvider implements ProviderInterface
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
    ) {
    }

    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Review
    {
        Assert::isInstanceOf($uriVariables['bookId'], BookId::class);

        Assert::isInstanceOf($uriVariables['id'], ReviewId::class);

        $book = $this->bookRepository->get($uriVariables['bookId']);
        foreach ($book->reviews as $review) {
            if ((string) $review->id === (string) $uriVariables['id']) {
                return $review;
            }
        }

        throw new MissingReviewException($uriVariables['bookId'], $uriVariables['id']);
    }
}
