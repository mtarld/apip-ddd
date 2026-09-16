<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Doctrine;

use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Uid\Uuid;

#[AsAlias(BookRepositoryInterface::class, when: ['dev', 'prod'])]
final readonly class DoctrineBookRepository implements BookRepositoryInterface
{
    private const string ALIAS = 'book';

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[\Override]
    public function add(Book $book): void
    {
        $this->em->persist($book);
    }

    #[\Override]
    public function remove(Book $book): void
    {
        $this->em->remove($book);
    }

    #[\Override]
    public function get(BookId $id): Book
    {
        return $this->em->find(Book::class, $id->value) ?? throw new MissingBookException($id);
    }

    #[\Override]
    public function idsByAuthor(AuthorId $authorId): iterable
    {
        /** @var iterable<array{bookId: string}> $result */
        $result = $this->em->createQueryBuilder()
            ->select(\sprintf('%s.id.value AS bookId', self::ALIAS))
            ->from(Book::class, self::ALIAS)
            ->where(\sprintf('%s.authorIdValue = :authorId', self::ALIAS))
            ->setParameter('authorId', $authorId->value)
            ->getQuery()
            ->toIterable([], AbstractQuery::HYDRATE_SCALAR);

        foreach ($result as $row) {
            yield new BookId(Uuid::fromString((string) $row['bookId']));
        }
    }
}
