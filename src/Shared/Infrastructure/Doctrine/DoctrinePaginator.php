<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\Repository\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @template T of object
 *
 * @implements PaginatorInterface<T>
 */
final class DoctrinePaginator implements PaginatorInterface
{
    private readonly int $firstResult;
    private readonly int $maxResults;

    /**
     * @param Paginator<T> $paginator
     */
    public function __construct(
        private readonly Paginator $paginator,
    ) {
        $firstResult = $paginator->getQuery()->getFirstResult();
        if (null === $firstResult) {
            throw new \InvalidArgumentException('Missing firstResult from the query.');
        }

        $maxResults = $paginator->getQuery()->getMaxResults();
        if (null === $maxResults) {
            throw new \InvalidArgumentException('Missing maxResults from the query.');
        }

        $this->firstResult = $firstResult;
        $this->maxResults = $maxResults;
    }

    public function getItemsPerPage(): int
    {
        return $this->maxResults;
    }

    public function getCurrentPage(): int
    {
        if (0 >= $this->maxResults) {
            return 1;
        }

        return (int) (1 + floor($this->firstResult / $this->maxResults));
    }

    public function getLastPage(): int
    {
        if (0 >= $this->maxResults) {
            return 1;
        }

        return (int) (ceil($this->getTotalItems() / $this->maxResults) ?: 1);
    }

    public function getTotalItems(): int
    {
        return count($this->paginator);
    }

    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    /**
     * @return \Traversable<T>
     */
    public function getIterator(): \Traversable
    {
        return $this->paginator->getIterator();
    }
}
