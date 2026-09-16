<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\InMemory;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsAlias(BookRepositoryInterface::class, public: true, when: ['test'])]
#[When('test')]
final class InMemoryBookRepository implements BookRepositoryInterface
{
    /** @var array<string, Book> */
    private array $books = [];

    #[\Override]
    public function add(Book $book): void
    {
        $this->books[(string) $book->id] = $book;
    }

    #[\Override]
    public function remove(Book $book): void
    {
        unset($this->books[(string) $book->id]);
    }

    #[\Override]
    public function get(BookId $id): Book
    {
        return $this->books[(string) $id] ?? throw new MissingBookException($id);
    }

    #[\Override]
    public function idsByAuthor(AuthorId $authorId): iterable
    {
        foreach ($this->books as $book) {
            if ((string) $book->authorId === (string) $authorId) {
                yield $book->id;
            }
        }
    }

    /**
     * @return array<string, Book>
     */
    public function books(): array
    {
        return $this->books;
    }
}
