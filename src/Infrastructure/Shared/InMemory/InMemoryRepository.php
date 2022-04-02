<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\InMemory;

use App\Domain\Shared\Repository\PaginatorInterface;
use App\Domain\Shared\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

abstract class InMemoryRepository implements RepositoryInterface
{
    protected array $entities = [];

    protected ?int $page = null;
    protected ?int $itemsPerPage = null;

    public function getIterator(): \Iterator
    {
        if (null !== $paginator = $this->paginator()) {
            yield from $paginator;

            return;
        }

        yield from $this->entities;
    }

    public function withPagination(int $page, int $itemsPerPage): static
    {
        Assert::positiveInteger($page);
        Assert::positiveInteger($itemsPerPage);

        $cloned = clone $this;
        $cloned->page = $page;
        $cloned->itemsPerPage = $itemsPerPage;

        return $cloned;
    }

    public function withoutPagination(): static
    {
        $cloned = clone $this;
        $cloned->page = null;
        $cloned->itemsPerPage = null;

        return $cloned;
    }

    public function paginator(): ?PaginatorInterface
    {
        if (null === $this->page || null === $this->itemsPerPage) {
            return null;
        }

        return new InMemoryPaginator($this->entities, $this->count(), $this->page, $this->itemsPerPage);
    }

    public function count(): int
    {
        return count($this->entities);
    }

    protected function filter(callable $filter): static
    {
        $cloned = clone $this;
        $cloned->entities = array_filter($cloned->entities, $filter);

        return $cloned;
    }
}
