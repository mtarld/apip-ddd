<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration;

use App\BookStore\Domain\Exception\MissingOrderException;
use App\BookStore\Domain\Model\Order;
use App\BookStore\Domain\Repository\OrderRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\OrderId;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Domain\ValueObject\PurchasedBook;
use App\BookStore\Domain\ValueObject\Quantity;

abstract class OrderRepositoryTestCase extends RepositoryTestCase
{
    protected const string PLACED_AT = '2026-03-01 10:00:00';

    public function testAnOrderRoundTripsWithItsLines(): void
    {
        $repository = $this->repository();

        $bookId = new BookId();
        $order = Order::place(new OrderId(),
            new \DateTimeImmutable(self::PLACED_AT),
            new PurchasedBook($bookId, new BookName('Dune'), new Price(1500), new Quantity(3)),
            new PurchasedBook(new BookId(), new BookName('Neuromancer'), new Price(1000), new Quantity(1)),
        );

        $repository->add($order);
        $this->persist();
        $this->detach();

        $found = $repository->get($order->id);

        self::assertCount(2, $found->lines);
        self::assertEquals(new Price(5500), $found->total);
        self::assertEquals(new \DateTimeImmutable(self::PLACED_AT), $found->placedAt);

        $line = $found->lines[0];
        self::assertSame((string) $bookId, (string) $line->bookId);
        self::assertEquals(new BookName('Dune'), $line->bookName);
        self::assertEquals(new Price(1500), $line->unitPrice);
        self::assertEquals(new Quantity(3), $line->quantity);
        self::assertEquals(new Price(4500), $line->subtotal);
    }

    public function testGetRefusesAnUnknownIdentifier(): void
    {
        $this->expectException(MissingOrderException::class);

        $this->repository()->get(new OrderId());
    }

    abstract protected function repository(): OrderRepositoryInterface;
}
