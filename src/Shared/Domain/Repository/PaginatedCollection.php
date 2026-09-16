<?php

declare(strict_types=1);

namespace App\Shared\Domain\Repository;

/**
 * @template T of object
 *
 * @implements \IteratorAggregate<array-key, T>
 */
final class PaginatedCollection implements \IteratorAggregate, \Countable
{
    public int $totalItems {
        get => $this->total ?? $this->count();
    }

    public int $lastPage {
        get => (int) \max(1, \ceil($this->totalItems / $this->pagination->itemsPerPage));
    }

    /**
     * @param iterable<array-key, T> $items
     */
    public function __construct(
        private readonly iterable $items,
        public readonly Pagination $pagination,
        private readonly ?int $total = null,
    ) {
    }

    /**
     * @return \Traversable<array-key, T>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->items;
    }

    public function count(): int
    {
        return \is_countable($this->items) ? \count($this->items) : \iterator_count($this->getIterator());
    }
}
