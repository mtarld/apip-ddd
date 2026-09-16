<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Infrastructure\ReadModel\BookView;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use Webmozart\Assert\Assert;

/**
 * @implements ProviderInterface<BookView>
 */
final readonly class BookItemProvider implements ProviderInterface
{
    public function __construct(
        private BookViewFinder $books,
    ) {
    }

    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): BookView
    {
        Assert::isInstanceOf($uriVariables['id'], BookId::class);

        return $this->books->get($uriVariables['id']);
    }
}
