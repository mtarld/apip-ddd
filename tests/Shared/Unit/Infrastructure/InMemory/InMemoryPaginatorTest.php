<?php

declare(strict_types=1);

namespace App\Tests\Shared\Unit\Infrastructure\InMemory;

use App\Shared\Infrastructure\InMemory\InMemoryPaginator;
use PHPUnit\Framework\TestCase;

final class InMemoryPaginatorTest extends TestCase
{
    /**
     * @dataProvider getLastPageDataProvider
     */
    public function testGetLastPage(int $lastPage, int $itemsPerPage): void
    {
        $items = [1, 2, 3];

        $paginator = new InMemoryPaginator(
            items: new \ArrayIterator($items),
            totalItems: count($items),
            currentPage: 1,
            itemsPerPage: $itemsPerPage,
        );

        static::assertSame($lastPage, $paginator->getLastPage());
    }

    public function getLastPageDataProvider(): iterable
    {
        yield [3, 1];
        yield [2, 2];
        yield [1, 3];
    }

    /**
     * @dataProvider iteratorDataProvider
     */
    public function testIterator(int $currentPage, int $itemsPerPage, array $page): void
    {
        $items = [1, 2, 3];

        $paginator = new InMemoryPaginator(
            items: new \ArrayIterator($items),
            totalItems: count($items),
            currentPage: $currentPage,
            itemsPerPage: $itemsPerPage,
        );

        static::assertSame(count($page), count($paginator));

        $i = 0;
        foreach ($paginator as $item) {
            static::assertSame($page[$i], $item);
            ++$i;
        }
    }

    public function iteratorDataProvider(): iterable
    {
        yield [1, 3, [1, 2, 3]];
        yield [2, 3, []];
        yield [2, 2, [3]];
        yield [1, 1, [1]];
        yield [2, 1, [2]];
        yield [3, 1, [3]];
        yield [4, 1, []];
    }
}
