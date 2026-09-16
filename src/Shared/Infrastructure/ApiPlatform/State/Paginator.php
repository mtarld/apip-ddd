<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination as ApiPlatformPagination;
use ApiPlatform\State\Pagination\PaginatorInterface;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;

/**
 * @template T of object
 *
 * @implements PaginatorInterface<T>
 * @implements \IteratorAggregate<T>
 */
final readonly class Paginator implements PaginatorInterface, \IteratorAggregate
{
    /**
     * @param \Traversable<T> $items
     */
    private function __construct(
        private \Traversable $items,
        private float $currentPage,
        private float $itemsPerPage,
        private float $lastPage,
        private float $totalItems,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public static function requested(ApiPlatformPagination $pagination, Operation $operation, array $context): ?Pagination
    {
        if (!$pagination->isEnabled($operation, $context)) {
            return null;
        }

        return new Pagination($pagination->getPage($context), $pagination->getLimit($operation, $context));
    }

    /**
     * @template TItem of object
     *
     * @param PaginatedCollection<TItem> $collection
     *
     * @return self<TItem>
     */
    public static function fromCollection(PaginatedCollection $collection): self
    {
        $items = \array_values(\iterator_to_array($collection));
        $pagination = $collection->pagination;

        return new self(
            new \ArrayIterator($items),
            (float) $pagination->page,
            (float) $pagination->itemsPerPage,
            (float) $collection->lastPage,
            (float) $collection->totalItems,
        );
    }

    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): float
    {
        return $this->itemsPerPage;
    }

    public function getLastPage(): float
    {
        return $this->lastPage;
    }

    public function getTotalItems(): float
    {
        return $this->totalItems;
    }

    public function count(): int
    {
        return $this->items instanceof \Countable ? \count($this->items) : \iterator_count($this->items);
    }

    /**
     * @return \Traversable<T>
     */
    public function getIterator(): \Traversable
    {
        return $this->items;
    }
}
