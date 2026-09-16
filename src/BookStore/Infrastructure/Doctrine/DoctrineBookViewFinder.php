<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Doctrine;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Model\Classification;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Infrastructure\ReadModel\BookView;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\BookStore\Infrastructure\ReadModel\ClassificationView;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

#[AsAlias(BookViewFinder::class, when: ['dev', 'prod'])]
final readonly class DoctrineBookViewFinder implements BookViewFinder
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[\Override]
    public function get(BookId $id): BookView
    {
        $summaries = $this->summaries(
            $this->createQueryBuilder()
                ->where('b.id.value = :id')
                ->setParameter('id', $id->value),
        );

        return $summaries[0] ?? throw new MissingBookException($id);
    }

    #[\Override]
    public function all(?Pagination $pagination = null): PaginatedCollection
    {
        return $this->page($this->createQueryBuilder(), $pagination);
    }

    #[\Override]
    public function byAuthor(AuthorId $authorId, ?Pagination $pagination = null): PaginatedCollection
    {
        return $this->page(
            $this->createQueryBuilder()
                ->where('b.authorIdValue = :authorId')
                ->setParameter('authorId', $authorId->value),
            $pagination,
        );
    }

    #[\Override]
    public function cheapest(int $size = 10): PaginatedCollection
    {
        return $this->page($this->createQueryBuilder()->orderBy('b.price.value', 'ASC'), new Pagination(1, $size));
    }

    /**
     * Grouped by the identifier alone: PostgreSQL knows the other columns are functionally
     * dependent on a primary key, so they need not be repeated in the GROUP BY.
     */
    private function createQueryBuilder(): QueryBuilder
    {
        return $this->em->createQueryBuilder()
            ->select(
                'b.id.value AS id',
                'b.isbn.value AS isbn',
                'b.name.value AS name',
                'b.description.value AS description',
                'b.authorIdValue AS authorId',
                'b.content.value AS content',
                'b.price.value AS price',
                'AVG(r.rating.value) AS averageRating',
            )
            ->from(Book::class, 'b')
            ->leftJoin('b.reviewCollection', 'r')
            ->groupBy('b.id.value');
    }

    /**
     * @return PaginatedCollection<BookView>
     */
    private function page(QueryBuilder $qb, ?Pagination $pagination): PaginatedCollection
    {
        $pagination ??= Pagination::default();

        $total = (clone $qb)
            ->resetDQLPart('groupBy')
            ->resetDQLPart('orderBy')
            ->select('COUNT(b.id.value)')
            ->getQuery()
            ->getSingleScalarResult();
        Assert::numeric($total);

        $qb->setFirstResult($pagination->offset())->setMaxResults($pagination->itemsPerPage);

        return new PaginatedCollection($this->summaries($qb), $pagination, (int) $total);
    }

    /**
     * @return list<BookView>
     */
    private function summaries(QueryBuilder $qb): array
    {
        /** @var list<array<string, mixed>> $rows */
        $rows = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        if ([] === $rows) {
            return [];
        }

        $classifications = $this->classificationsOf(\array_map(static fn (array $row): string => self::text($row, 'id'), $rows));

        return \array_map(static fn (array $row): BookView => new BookView(
            new BookId(Uuid::fromString(self::text($row, 'id'))),
            self::text($row, 'isbn'),
            self::text($row, 'name'),
            self::text($row, 'description'),
            self::text($row, 'authorId'),
            self::text($row, 'content'),
            (int) self::number($row, 'price'),
            null === ($row['averageRating'] ?? null) ? null : (float) self::number($row, 'averageRating'),
            $classifications[self::text($row, 'id')] ?? [],
        ), $rows);
    }

    /**
     * @param list<string> $bookIds
     *
     * @return array<string, list<ClassificationView>>
     */
    private function classificationsOf(array $bookIds): array
    {
        /** @var list<array<string, mixed>> $rows */
        $rows = $this->em->createQueryBuilder()
            ->select('b.id.value AS bookId', 'cat.id.value AS categoryId', 'c.classifiedAt AS classifiedAt')
            ->from(Classification::class, 'c')
            ->join('c.book', 'b')
            ->join('c.category', 'cat')
            ->where('b.id.value IN (:ids)')
            ->setParameter('ids', $bookIds)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $byBook = [];
        foreach ($rows as $row) {
            $classifiedAt = $row['classifiedAt'] ?? null;
            Assert::isInstanceOf($classifiedAt, \DateTimeImmutable::class);

            $byBook[self::text($row, 'bookId')][] = new ClassificationView(self::text($row, 'categoryId'), $classifiedAt);
        }

        return $byBook;
    }

    /**
     * Scalar hydration hands back `mixed` per column - and identifiers arrive as AbstractUid
     * through their DBAL type - so the projection narrows once here rather than scattering casts
     * the analyser cannot check.
     *
     * @param array<string, mixed> $row
     */
    private static function text(array $row, string $column): string
    {
        $value = $row[$column] ?? null;
        Assert::true(\is_scalar($value) || $value instanceof AbstractUid, \sprintf('Expected a readable value in column "%s".', $column));

        return (string) $value;
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function number(array $row, string $column): string
    {
        Assert::numeric($value = $row[$column] ?? null, \sprintf('Expected a number in column "%s".', $column));

        return (string) $value;
    }
}
