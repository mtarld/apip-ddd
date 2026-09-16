<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\BookStore\Domain\ValueObject\ReviewRating;
use App\BookStore\Infrastructure\ApiPlatform\Resource\ReviewResource;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Component\Uid\Uuid;

final class BookReviewsTest extends ApiTestCase
{
    public function testReviewABook(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        $response = $client->request('POST', \sprintf('/api/books/%s/reviews', $book->id), [
            'json' => [
                'rating' => 5,
                'comment' => 'A masterpiece.',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(ReviewResource::class);

        self::assertJsonContains([
            'rating' => 5,
            'comment' => 'A masterpiece.',
            'book' => \sprintf('/api/books/%s', $book->id),
        ]);

        $iri = $response->toArray()['@id'];
        self::assertIsString($iri);
        self::assertStringStartsWith(\sprintf('/api/books/%s/reviews/', $book->id), $iri);

        self::assertCount(1, $book->reviews);
    }

    public function testReturnTheReviewsOfABook(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $book->review(new ReviewId(), new ReviewRating(5), new ReviewComment('Great.'), $this->at());
        $book->review(new ReviewId(), new ReviewRating(2), new ReviewComment('Meh.'), $this->at());
        $bookRepository->add($book);

        $client->request('GET', \sprintf('/api/books/%s/reviews', $book->id));

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceCollectionJsonSchema(ReviewResource::class);

        self::assertJsonContains([
            'totalItems' => 2,
            'member' => [
                ['rating' => 5, 'comment' => 'Great.'],
                ['rating' => 2, 'comment' => 'Meh.'],
            ],
        ]);
    }

    public function testExposeTheAverageRatingOnTheBook(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $book->review(new ReviewId(), new ReviewRating(5), new ReviewComment('Great.'), $this->at());
        $book->review(new ReviewId(), new ReviewRating(2), new ReviewComment('Meh.'), $this->at());
        $bookRepository->add($book);

        $client->request('GET', \sprintf('/api/books/%s', $book->id));

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['averageRating' => 3.5]);
    }

    public function testAReviewLinksToItsBookWithoutRebuildingIt(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $book->review(new ReviewId(), new ReviewRating(5), new ReviewComment('Great.'), $this->at());
        $bookRepository->add($book);

        $response = $client->request('GET', \sprintf('/api/books/%s/reviews', $book->id));

        self::assertResponseIsSuccessful();

        $member = $response->toArray()['member'];
        self::assertIsArray($member);
        self::assertIsArray($member[0]);
        self::assertSame(\sprintf('/api/books/%s', $book->id), $member[0]['book']);
    }

    public function testReturnASingleReviewThroughItsBook(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $book->review(new ReviewId(), new ReviewRating(4), new ReviewComment('Worth it.'), $this->at());
        $review = $book->review(new ReviewId(), new ReviewRating(2), new ReviewComment('Meh.'), $this->at());
        $bookRepository->add($book);

        $client->request('GET', \sprintf('/api/books/%s/reviews/%s', $book->id, $review->id));

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(ReviewResource::class);
        self::assertJsonContains([
            'rating' => 2,
            'comment' => 'Meh.',
            'book' => \sprintf('/api/books/%s', $book->id),
        ]);
    }

    public function testCannotReadAReviewThroughAnotherBook(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $review = $book->review(new ReviewId(), new ReviewRating(5), new ReviewComment('Great.'), $this->at());
        $bookRepository->add($book);

        $otherBook = BookFactory::create();
        $bookRepository->add($otherBook);

        $client->request('GET', \sprintf('/api/books/%s/reviews/%s', $otherBook->id, $review->id));

        self::assertResponseStatusCodeSame(404);
    }

    public function testCannotReviewAMissingBook(): void
    {
        $client = self::createClient();

        $client->request('POST', \sprintf('/api/books/%s/reviews', Uuid::v4()), [
            'json' => [
                'rating' => 5,
                'comment' => 'A masterpiece.',
            ],
        ]);

        self::assertResponseStatusCodeSame(404);
    }

    public function testCannotPostAnInvalidReview(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        $client->request('POST', \sprintf('/api/books/%s/reviews', $book->id), [
            'json' => [
                'rating' => 42,
                'comment' => '',
            ],
        ]);

        self::assertResponseIsUnprocessable();
    }

    private function at(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2026-03-01 10:00:00');
    }
}
