<?php

declare(strict_types=1);

namespace App\Tests\Shared\Unit\Domain\Repository;

use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PaginatedCollectionTest extends TestCase
{
    public function testItCountsItsPageButKnowsTheTotal(): void
    {
        $collection = new PaginatedCollection(
            [new \stdClass(), new \stdClass()],
            new Pagination(2, 2),
            total: 5,
        );

        self::assertCount(2, $collection);
        self::assertSame(5, $collection->totalItems);
        self::assertSame(2, $collection->pagination->page);
    }

    public function testTheTotalFallsBackToThePageWhenNoneWasGiven(): void
    {
        $collection = new PaginatedCollection([new \stdClass(), new \stdClass()], Pagination::default());

        self::assertSame(2, $collection->totalItems);
        self::assertSame(1, $collection->lastPage);
    }

    /**
     * @param positive-int $itemsPerPage
     */
    #[DataProvider('lastPageProvider')]
    public function testLastPage(int $total, int $itemsPerPage, int $expected): void
    {
        $collection = new PaginatedCollection([], new Pagination(1, $itemsPerPage), total: $total);

        self::assertSame($expected, $collection->lastPage);
    }

    /**
     * @return iterable<string, array{int, positive-int, int}>
     */
    public static function lastPageProvider(): iterable
    {
        yield 'exact fit' => [10, 5, 2];
        yield 'partial last page' => [11, 5, 3];
        yield 'single item' => [1, 5, 1];
        yield 'empty is still one page' => [0, 5, 1];
    }

    public function testItIteratesOverATraversable(): void
    {
        $items = (static function (): \Generator {
            yield new \stdClass();
            yield new \stdClass();
            yield new \stdClass();
        })();

        $collection = new PaginatedCollection($items, Pagination::default());

        self::assertCount(3, \iterator_to_array($collection));
    }
}
