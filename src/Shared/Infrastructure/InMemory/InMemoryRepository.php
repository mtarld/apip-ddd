<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\InMemory;

use App\Shared\Domain\Repository\PaginatorInterface;
use App\Shared\Domain\Repository\RepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;

/**
 * @template T of object
 *
 * @implements RepositoryInterface<T>
 */
abstract class InMemoryRepository implements RepositoryInterface
{
    /**
     * @var array<string, T>
     */
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

        return new InMemoryPaginator(
            new \ArrayIterator($this->entities),
            count($this->entities),
            $this->page,
            $this->itemsPerPage,
        );
    }

    public function count(): int
    {
        if (null !== $paginator = $this->paginator()) {
            return count($paginator);
        }

        return count($this->entities);
    }

    /**
     * @param callable(mixed, mixed=): bool $filter
     *
     * @return static<T>
     */
    protected function filter(callable $filter): static
    {
        $cloned = clone $this;
        $cloned->entities = array_filter($cloned->entities, $filter);

        return $cloned;
    }

    /**
     * @param array<string, string> $criteria
     * @return $this
     */
    protected function sort(array $criteria): static
    {
        $cloned = clone $this;

        $accessor = PropertyAccess::createPropertyAccessor();

        uasort($cloned->entities, function (object $a, object $b) use ($criteria, $accessor): int {
            foreach ($criteria as $field => $direction) {
                $aValue = $accessor->getValue($a, $field);
                $bValue = $accessor->getValue($b, $field);

                $comparison = $aValue <=> $bValue;
                if (0 !== $comparison) {
                    return 'asc' === $direction ? $comparison : -$comparison;
                }
            }

            return 0;
        });

        return $cloned;
    }
}
