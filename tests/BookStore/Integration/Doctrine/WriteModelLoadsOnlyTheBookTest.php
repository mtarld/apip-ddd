<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\Doctrine;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\BookStore\Domain\ValueObject\ReviewRating;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookRepository;
use App\Tests\BookStore\Factory\BookFactory;
use App\Tests\BookStore\Integration\RepositoryTestCase;

final class WriteModelLoadsOnlyTheBookTest extends RepositoryTestCase
{
    use RunsAgainstDoctrine;

    public function testLoadingABookDoesNotLoadItsReviews(): void
    {
        $book = BookFactory::create();
        for ($i = 0; $i < 5; ++$i) {
            $book->review(new ReviewId(), new ReviewRating(4), new ReviewComment('c'.$i), new \DateTimeImmutable('2026-03-01 10:00:00'));
        }
        $this->repository()->add($book);
        $this->persist();
        $this->detach();

        $loaded = $this->repository()->get($book->id);

        self::assertSame(1, self::$em->getUnitOfWork()->size());
        self::assertSame((string) $book->id, (string) $loaded->id);
    }

    public function testTheReviewsAreStillThereWhenSomethingAsksForThem(): void
    {
        $book = BookFactory::create();
        $book->review(new ReviewId(), new ReviewRating(4), new ReviewComment('Solid.'), new \DateTimeImmutable('2026-03-01 10:00:00'));
        $this->repository()->add($book);
        $this->persist();
        $this->detach();

        self::assertCount(1, $this->repository()->get($book->id)->reviews);
    }

    /**
     * @return list<string>
     */
    protected static function tablesToTruncate(): array
    {
        return ['book'];
    }

    private function repository(): BookRepositoryInterface
    {
        return self::getContainer()->get(DoctrineBookRepository::class);
    }
}
