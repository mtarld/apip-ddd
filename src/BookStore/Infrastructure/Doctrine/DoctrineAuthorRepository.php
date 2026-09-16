<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Doctrine;

use App\BookStore\Domain\Exception\MissingAuthorException;
use App\BookStore\Domain\Model\Author;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\Shared\Domain\Repository\PaginatedCollection;
use App\Shared\Domain\Repository\Pagination;
use App\Shared\Infrastructure\Doctrine\PaginatesWithDoctrine;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(AuthorRepositoryInterface::class, when: ['dev', 'prod'])]
final readonly class DoctrineAuthorRepository implements AuthorRepositoryInterface
{
    /** @use PaginatesWithDoctrine<Author> */
    use PaginatesWithDoctrine;

    private const string ALIAS = 'author';

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[\Override]
    public function add(Author $author): void
    {
        $this->em->persist($author);
    }

    #[\Override]
    public function get(AuthorId $id): Author
    {
        return $this->em->find(Author::class, $id->value) ?? throw new MissingAuthorException($id);
    }

    #[\Override]
    public function findByName(AuthorName $name): ?Author
    {
        /** @var ?Author */
        return $this->createQueryBuilder()
            ->where(\sprintf('%s.name.value = :name', self::ALIAS))
            ->setParameter('name', $name->value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    #[\Override]
    public function all(?Pagination $pagination = null): PaginatedCollection
    {
        return $this->paginate($this->createQueryBuilder(), $pagination);
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->em->createQueryBuilder()
            ->select(self::ALIAS)
            ->from(Author::class, self::ALIAS);
    }
}
