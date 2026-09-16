<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Unit;

use App\BookStore\Application\Command\PlaceOrderCommand;
use App\BookStore\Domain\Event\BookPublished;
use App\BookStore\Domain\Model\Category;
use App\BookStore\Domain\Model\Order;
use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\BookStore\Domain\ValueObject\CategoryName;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Domain\ValueObject\Isbn;
use App\BookStore\Domain\ValueObject\OrderId;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Domain\ValueObject\PurchasedBook;
use App\BookStore\Domain\ValueObject\Quantity;
use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\BookStore\Domain\ValueObject\ReviewRating;
use App\Tests\BookStore\Factory\BookFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class AggregateInvariantsTest extends TestCase
{
    private const string AT = '2026-03-01 10:00:00';

    public function testAnAggregateHandsOutACopyOfItsChildren(): void
    {
        $book = BookFactory::create();
        $book->review(new ReviewId(), new ReviewRating(5), new ReviewComment('Great.'), $this->at());

        $reviews = $book->reviews;
        $reviews[] = 'smuggled in';

        self::assertCount(1, $book->reviews);
    }

    public function testTheOnlyWayToReviewIsThroughTheBook(): void
    {
        $book = BookFactory::create();

        self::assertCount(0, $book->reviews);

        $review = $book->review(new ReviewId(), new ReviewRating(4), new ReviewComment('Solid.'), $this->at());

        self::assertCount(1, $book->reviews);
        self::assertSame($book, $review->book);
    }

    public function testClassificationIsDecidedByIdentityNotByInstance(): void
    {
        $book = BookFactory::create();
        $category = new Category(new CategoryId(), new CategoryName('Science fiction'));

        $book->classify($category, $this->at());

        $sameCategory = clone $category;

        self::assertTrue($book->isClassifiedAs($sameCategory));

        $book->classify($sameCategory, $this->at());
        self::assertCount(1, $book->categories);

        $book->declassify($sameCategory);
        self::assertCount(0, $book->categories);
    }

    public function testAClassificationRemembersWhenItHappened(): void
    {
        $book = BookFactory::create();
        $category = new Category(new CategoryId(), new CategoryName('Science fiction'));

        $book->classify($category, $this->at());

        self::assertCount(1, $book->classifications);
        self::assertSame($category, $book->classifications[0]->category);
        self::assertEquals(new \DateTimeImmutable(self::AT), $book->classifications[0]->classifiedAt);
    }

    public function testThePlaceOrderCommandRefusesAnEmptyBasket(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('An order must contain at least one item.');

        // @phpstan-ignore argument.type (the empty list is exactly what is under test)
        new PlaceOrderCommand(new OrderId(), []);
    }

    public function testAnOrderTotalsItsOwnLines(): void
    {
        $order = Order::place(new OrderId(),
            $this->at(),
            new PurchasedBook(new BookId(), new BookName('Dune'), new Price(1500), new Quantity(2)),
            new PurchasedBook(new BookId(), new BookName('Neuromancer'), new Price(1000), new Quantity(1)),
        );

        self::assertEquals(new Price(4000), $order->total);
        self::assertCount(2, $order->lines);
    }

    public function testAnOrderCannotExistWithoutLines(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('An order must contain at least one item.');

        Order::place(new OrderId(), $this->at());
    }

    public function testAnAggregateRecordsWhatHappenedWithoutPublishingIt(): void
    {
        $authorId = new AuthorId();
        $book = BookFactory::create(authorId: $authorId, price: 1200);

        $events = $book->releaseEvents();

        self::assertEquals(
            [new BookPublished($book->id, $book->name, $authorId, new Price(1200))],
            $events,
        );
    }

    public function testRecordedEventsAreReleasedOnce(): void
    {
        $book = BookFactory::create();

        self::assertCount(1, $book->releaseEvents());
        self::assertSame([], $book->releaseEvents());
    }

    #[DataProvider('discounts')]
    public function testADiscountComesOffThePrice(int $price, int $percentage, int $expected): void
    {
        self::assertEquals(new Price($expected), new Price($price)->applyDiscount(new Discount($percentage)));
    }

    /**
     * @return iterable<array{int, int<0, 100>, int}>
     */
    public static function discounts(): iterable
    {
        yield [100, 0, 100];
        yield [100, 20, 80];
        yield [50, 30, 35];
        yield [50, 100, 0];
    }

    public function testAPriceCannotBeNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Price(-1);
    }

    public function testAnIsbnRefusesABadCheckDigit(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Isbn('9782123456804');
    }

    public function testAnAnonymizationAlwaysCarriesAReason(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new AnonymizationReason('');
    }

    private function at(): \Symfony\Component\Clock\DatePoint
    {
        return new MockClock(self::AT)->now();
    }
}
