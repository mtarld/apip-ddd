<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Infrastructure\ReadModel\BookView;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use Webmozart\Assert\Assert;

/**
 * @implements ProviderInterface<BookView>
 */
final readonly class CheapestBooksProvider implements ProviderInterface
{
    public function __construct(
        private BookViewFinder $books,
    ) {
    }

    /**
     * @return list<BookView>
     */
    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $size = $operation->getParameters()?->get('size')?->getValue();
        Assert::positiveInteger($size);

        return \array_values(\iterator_to_array($this->books->cheapest($size)));
    }
}
