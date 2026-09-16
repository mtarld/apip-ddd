<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\ReviewBookCommand;
use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\BookStore\Domain\ValueObject\ReviewRating;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ReviewBookTest extends KernelTestCase
{
    public function testReviewBook(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        $commandBus->dispatch(new ReviewBookCommand(
            $book->id,
            new ReviewId(),
            new ReviewRating(4),
            new ReviewComment('Solid.'),
        ));

        self::assertCount(1, $book->reviews);
        $review = $book->reviews[0];
        self::assertSame($book, $review->book);
        self::assertEquals(new ReviewRating(4), $review->rating);
        self::assertEquals(new ReviewComment('Solid.'), $review->comment);
    }

    public function testAverageRatingIsComputedByTheReadModel(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var BookViewFinder $books */
        $books = self::getContainer()->get(BookViewFinder::class);
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        self::assertNull($books->get($book->id)->averageRating);

        foreach ([5, 2] as $rating) {
            $commandBus->dispatch(new ReviewBookCommand(
                $book->id,
                new ReviewId(),
                new ReviewRating($rating),
                new ReviewComment('A comment.'),
            ));
        }

        self::assertSame(3.5, $books->get($book->id)->averageRating);
    }

    public function testCannotReviewAMissingBook(): void
    {
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $this->expectException(MissingBookException::class);

        $commandBus->dispatch(new ReviewBookCommand(
            new BookId(),
            new ReviewId(),
            new ReviewRating(4),
            new ReviewComment('Solid.'),
        ));
    }
}
