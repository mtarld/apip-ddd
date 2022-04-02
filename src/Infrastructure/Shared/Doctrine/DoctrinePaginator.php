<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Doctrine;

use App\Domain\Shared\Repository\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class DoctrinePaginator implements PaginatorInterface
{
    private ?int $firstResult;
    private ?int $maxResults;

    public function __construct(
        private Paginator $paginator
    ) {
        $this->firstResult = $paginator->getQuery()->getFirstResult();
        $this->maxResults = $paginator->getQuery()->getMaxResults();

        if (null === $this->firstResult || null === $this->maxResults) {
            throw new \InvalidArgumentException('Missing firstResult and maxResults from the query.');
        }
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

    public function getIterator(): \Traversable
    {
        return $this->paginator->getIterator();
    }
}
