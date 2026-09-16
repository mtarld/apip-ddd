<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\InMemory;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Model\Classification;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Infrastructure\ReadModel\BookView;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\BookStore\Infrastructure\ReadModel\ClassificationView;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;
use App\Shared\Infrastructure\InMemory\PaginatesInMemory;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsAlias(BookViewFinder::class, public: true, when: ['test'])]
#[When('test')]
final readonly class InMemoryBookViewFinder implements BookViewFinder
{
    use PaginatesInMemory;

    public function __construct(
        private InMemoryBookRepository $repository,
    ) {
    }

    #[\Override]
    public function get(BookId $id): BookView
    {
        return $this->viewOf($this->repository->get($id));
    }

    #[\Override]
    public function all(?Pagination $pagination = null): PaginatedCollection
    {
        return $this->project($this->repository->books(), $pagination);
    }

    #[\Override]
    public function byAuthor(AuthorId $authorId, ?Pagination $pagination = null): PaginatedCollection
    {
        return $this->project(
            \array_filter(
                $this->repository->books(),
                static fn (Book $book): bool => (string) $book->authorId === (string) $authorId,
            ),
            $pagination,
        );
    }

    #[\Override]
    public function cheapest(int $size = 10): PaginatedCollection
    {
        $sorted = $this->repository->books();
        \uasort($sorted, static fn (Book $a, Book $b): int => $a->price->value <=> $b->price->value);

        return $this->project($sorted, new Pagination(1, $size));
    }

    /**
     * @param array<string, Book> $books
     *
     * @return PaginatedCollection<BookView>
     */
    private function project(array $books, ?Pagination $pagination): PaginatedCollection
    {
        $page = $this->paginate($books, $pagination);

        $views = [];
        foreach ($page as $book) {
            $views[] = $this->viewOf($book);
        }

        return new PaginatedCollection($views, $page->pagination, $page->totalItems);
    }

    private function viewOf(Book $book): BookView
    {
        $ratings = \array_map(static fn (object $review): int => $review->rating->value, $book->reviews);

        return new BookView(
            $book->id,
            $book->isbn->value,
            $book->name->value,
            $book->description->value,
            (string) $book->authorId,
            $book->content->value,
            $book->price->value,
            [] === $ratings ? null : \array_sum($ratings) / \count($ratings),
            \array_map(
                static fn (Classification $classification): ClassificationView => new ClassificationView(
                    (string) $classification->category->id,
                    $classification->classifiedAt,
                ),
                $book->classifications,
            ),
        );
    }
}
