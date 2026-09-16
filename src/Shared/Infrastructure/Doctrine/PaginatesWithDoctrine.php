<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @template T of object
 */
trait PaginatesWithDoctrine
{
    /**
     * @return PaginatedCollection<T>
     */
    private function paginate(QueryBuilder $qb, ?Pagination $pagination): PaginatedCollection
    {
        $pagination ??= Pagination::default();

        $qb->setFirstResult($pagination->offset())
            ->setMaxResults($pagination->itemsPerPage);

        /** @var Paginator<T> $paginator */
        $paginator = new Paginator($qb->getQuery());

        return new PaginatedCollection(\iterator_to_array($paginator), $pagination, \count($paginator));
    }
}
