<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Doctrine;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookId;
use App\Shared\Infrastructure\Doctrine\DoctrineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends DoctrineRepository<Book>
 */
final class DoctrineBookRepository extends DoctrineRepository implements BookRepositoryInterface
{
    private const ENTITY_CLASS = Book::class;
    private const ALIAS = 'book';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, self::ENTITY_CLASS, self::ALIAS);
    }

    public function save(Book $book): void
    {
        $this->em->persist($book);
        $this->em->flush();
    }

    public function remove(Book $book): void
    {
        $this->em->remove($book);
        $this->em->flush();
    }

    public function ofId(BookId $id): ?Book
    {
        return $this->em->find(self::ENTITY_CLASS, $id->value);
    }

    public function withAuthor(Author $author): static
    {
        return $this->filter(static function (QueryBuilder $qb) use ($author): void {
            $qb->where(sprintf('%s.author.value = :author', self::ALIAS))->setParameter('author', $author->value);
        });
    }

    public function withCheapestsFirst(): static
    {
        return $this->filter(static function (QueryBuilder $qb): void {
            $qb->orderBy(sprintf('%s.price.amount', self::ALIAS), 'ASC');
        });
    }
}
