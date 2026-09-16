<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\InMemory;

use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;

trait PaginatesInMemory
{
    /**
     * @template T of object
     *
     * @param array<array-key, T> $items
     *
     * @return PaginatedCollection<T>
     */
    private function paginate(array $items, ?Pagination $pagination): PaginatedCollection
    {
        $pagination ??= Pagination::default();

        return new PaginatedCollection(
            \array_slice($items, $pagination->offset(), $pagination->itemsPerPage, preserve_keys: true),
            $pagination,
            \count($items),
        );
    }
}
