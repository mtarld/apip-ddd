<?php

declare(strict_types=1);

namespace App\Infrastructure\Library\Doctrine;

use App\Domain\Library\Model\Book;
use App\Domain\Library\Repository\BookRepositoryInterface;
use App\Infrastructure\Shared\Doctrine\DoctrineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;

final class DoctrineBookRepository extends DoctrineRepository implements BookRepositoryInterface
{
    private const ENTITY_CLASS = Book::class;
    private const ALIAS = 'book';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, self::ENTITY_CLASS, self::ALIAS);
    }

    public function add(Book $book): void
    {
        $this->em->persist($book);
        $this->em->flush();
    }

    public function remove(Book $book): void
    {
        $this->em->remove($book);
        $this->em->flush();
    }

    public function ofId(Uuid $id): ?Book
    {
        return $this->em->find(self::ENTITY_CLASS, $id);
    }

    public function withAuthor(string $author): static
    {
        return $this->filter(static function (QueryBuilder $qb) use ($author): void {
            $qb->where(sprintf('%s.author = :author', self::ALIAS))->setParameter('author', $author);
        });
    }

    public function withCheapestsFirst(): static
    {
        return $this->filter(static function (QueryBuilder $qb): void {
            $qb->orderBy(sprintf('%s.price', self::ALIAS), 'ASC');
        });
    }
}
