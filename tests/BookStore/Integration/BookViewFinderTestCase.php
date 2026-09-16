<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\BookStore\Domain\ValueObject\ReviewRating;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Domain\Repository\Pagination;
use App\Tests\BookStore\Factory\BookFactory;
use App\Tests\BookStore\Factory\CategoryFactory;

abstract class BookViewFinderTestCase extends RepositoryTestCase
{
    protected const string AT = '2026-03-01 10:00:00';

    public function testItProjectsABook(): void
    {
        $book = BookFactory::create(name: 'Dune', description: 'A desert planet', price: 1500);
        $this->books()->add($book);
        $this->persist();
        $this->detach();

        $view = $this->finder()->get($book->id);

        self::assertSame((string) $book->id, (string) $view->id);
        self::assertSame('Dune', $view->name);
        self::assertSame('A desert planet', $view->description);
        self::assertSame((string) $book->authorId, $view->authorId);
        self::assertSame(1500, $view->price);
    }

    public function testABookWithoutReviewsHasNoAverage(): void
    {
        $book = BookFactory::create();
        $this->books()->add($book);
        $this->persist();
        $this->detach();

        self::assertNull($this->finder()->get($book->id)->averageRating);
    }

    public function testItAveragesTheReviews(): void
    {
        $book = BookFactory::create();
        $book->review(new ReviewId(), new ReviewRating(5), new ReviewComment('Great.'), new \DateTimeImmutable(self::AT));
        $book->review(new ReviewId(), new ReviewRating(2), new ReviewComment('Meh.'), new \DateTimeImmutable(self::AT));
        $this->books()->add($book);
        $this->persist();
        $this->detach();

        self::assertSame(3.5, $this->finder()->get($book->id)->averageRating);
    }

    public function testItCarriesTheClassifications(): void
    {
        $category = CategoryFactory::create('Science Fiction');
        $this->categories()->add($category);

        $book = BookFactory::create();
        $book->classify($category, new \DateTimeImmutable(self::AT));
        $this->books()->add($book);
        $this->persist();
        $this->detach();

        $view = $this->finder()->get($book->id);

        self::assertCount(1, $view->classifications);
        self::assertSame((string) $category->id, $view->classifications[0]->category);
        self::assertEquals(new \DateTimeImmutable(self::AT), $view->classifications[0]->classifiedAt);
    }

    public function testGetRefusesAnUnknownIdentifier(): void
    {
        $this->expectException(MissingBookException::class);

        $this->finder()->get(new BookId());
    }

    public function testAllIsPaginated(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->books()->add(BookFactory::create());
        }
        $this->persist();
        $this->detach();

        $collection = $this->finder()->all(new Pagination(1, 2));

        self::assertCount(2, $collection);
        self::assertSame(5, $collection->totalItems);
        self::assertSame(3, $collection->lastPage);
    }

    public function testByAuthor(): void
    {
        $target = new AuthorId();

        $this->books()->add(BookFactory::create(authorId: $target));
        $this->books()->add(BookFactory::create(authorId: $target));
        $this->books()->add(BookFactory::create(authorId: new AuthorId()));
        $this->persist();
        $this->detach();

        self::assertCount(2, $this->finder()->byAuthor($target));
    }

    public function testCheapestIsOrderedAndLimited(): void
    {
        foreach ([300, 100, 200] as $price) {
            $this->books()->add(BookFactory::create(price: $price));
        }
        $this->persist();
        $this->detach();

        $prices = [];
        foreach ($this->finder()->cheapest(2) as $view) {
            $prices[] = $view->price;
        }

        self::assertSame([100, 200], $prices);
    }

    abstract protected function finder(): BookViewFinder;

    abstract protected function books(): BookRepositoryInterface;

    abstract protected function categories(): CategoryRepositoryInterface;
}
