<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\InMemory;

use App\BookStore\Domain\Exception\MissingAuthorException;
use App\BookStore\Domain\Model\Author;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;
use App\Shared\Infrastructure\InMemory\PaginatesInMemory;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsAlias(AuthorRepositoryInterface::class, public: true, when: ['test'])]
#[When('test')]
final class InMemoryAuthorRepository implements AuthorRepositoryInterface
{
    use PaginatesInMemory;

    /** @var array<string, Author> */
    private array $authors = [];

    #[\Override]
    public function add(Author $author): void
    {
        $this->authors[(string) $author->id] = $author;
    }

    #[\Override]
    public function get(AuthorId $id): Author
    {
        return $this->authors[(string) $id] ?? throw new MissingAuthorException($id);
    }

    #[\Override]
    public function findByName(AuthorName $name): ?Author
    {
        foreach ($this->authors as $author) {
            if ($author->name->equals($name)) {
                return $author;
            }
        }

        return null;
    }

    #[\Override]
    public function all(?Pagination $pagination = null): PaginatedCollection
    {
        return $this->paginate($this->authors, $pagination);
    }
}
