<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\Doctrine;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookRepository;
use App\Tests\BookStore\Factory\BookFactory;
use App\Tests\BookStore\Integration\BookRepositoryTestCase;
use Doctrine\ORM\OptimisticLockException;

final class DoctrineBookRepositoryTest extends BookRepositoryTestCase
{
    use RunsAgainstDoctrine;

    public function testIdsByAuthorHydratesNoBook(): void
    {
        $target = new AuthorId();

        $repository = $this->repository();
        for ($i = 0; $i < 3; ++$i) {
            $repository->add(BookFactory::create(authorId: $target));
        }
        $this->persist();
        $this->detach();

        self::assertCount(3, \iterator_to_array($repository->idsByAuthor($target), false));

        self::assertSame(0, self::$em->getUnitOfWork()->size());
    }

    public function testAConcurrentWriteLosesInsteadOfSilentlyWinning(): void
    {
        $repository = $this->repository();

        $book = BookFactory::create(price: 1000);
        $repository->add($book);
        $this->persist();

        self::$em->getConnection()->executeStatement(
            'UPDATE book SET price = 500, version = version + 1 WHERE id = ?',
            [(string) $book->id],
        );

        $book->applyDiscount(new Discount(10));

        $this->expectException(OptimisticLockException::class);
        $this->persist();
    }

    protected function repository(): BookRepositoryInterface
    {
        return self::getContainer()->get(DoctrineBookRepository::class);
    }

    /**
     * @return list<string>
     */
    protected static function tablesToTruncate(): array
    {
        return ['book'];
    }
}
