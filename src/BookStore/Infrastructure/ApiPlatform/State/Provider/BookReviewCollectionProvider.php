<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Domain\Model\Review;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use Webmozart\Assert\Assert;

/**
 * @implements ProviderInterface<Review>
 */
final readonly class BookReviewCollectionProvider implements ProviderInterface
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
    ) {
    }

    /**
     * @return list<Review>
     */
    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        Assert::isInstanceOf($uriVariables['bookId'], BookId::class);

        $book = $this->bookRepository->get($uriVariables['bookId']);

        return $book->reviews;
    }
}
