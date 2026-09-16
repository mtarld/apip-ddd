<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination as ApiPlatformPagination;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Infrastructure\ApiPlatform\Resource\AuthorResource;
use App\BookStore\Infrastructure\ReadModel\BookView;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Infrastructure\ApiPlatform\State\Paginator;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProviderInterface<BookView>
 */
final readonly class BookCollectionProvider implements ProviderInterface
{
    public function __construct(
        private BookViewFinder $books,
        private ApiPlatformPagination $pagination,
    ) {
    }

    /**
     * @return Paginator<BookView>
     */
    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Paginator
    {
        $author = $operation->getParameters()?->get('author')?->getValue();

        $pagination = Paginator::requested($this->pagination, $operation, $context);

        $collection = $author instanceof AuthorResource
            ? $this->books->byAuthor(new AuthorId(Uuid::fromString($author->id)), $pagination)
            : $this->books->all($pagination);

        return Paginator::fromCollection($collection);
    }
}
