<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\InMemory;

use App\Domain\Shared\Repository\PaginatorInterface;

final class InMemoryPaginator implements PaginatorInterface
{
    private int $offset;
    private int $limit;
    private int $lastPage;

    public function __construct(
        private iterable $items,
        private int $totalItems,
        private int $currentPage,
        private int $itemsPerPage,
    ) {
        $this->offset = ($currentPage - 1) * $itemsPerPage;
        $this->limit = $this->offset + $itemsPerPage;
        $this->lastPage = (int) max(1, ceil($totalItems / $itemsPerPage));
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    public function getIterator(): \Traversable
    {
        if ($this->currentPage > $this->lastPage) {
            return new \EmptyIterator();
        }

        return new \LimitIterator(new \ArrayIterator($this->items), $this->offset, $this->limit);
    }
}
