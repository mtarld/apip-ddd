<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\PlaceOrderCommand;
use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\OrderRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\OrderId;
use App\BookStore\Domain\ValueObject\OrderItem;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Domain\ValueObject\Quantity;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PlaceOrderTest extends KernelTestCase
{
    public function testPlaceOrder(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = self::getContainer()->get(OrderRepositoryInterface::class);
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $dune = BookFactory::create(name: 'Dune', price: 1500);
        $neuromancer = BookFactory::create(name: 'Neuromancer', price: 1000);
        $bookRepository->add($dune);
        $bookRepository->add($neuromancer);

        $orderId = new OrderId();
        $commandBus->dispatch(new PlaceOrderCommand($orderId, [
            new OrderItem($dune->id, new Quantity(3)),
            new OrderItem($neuromancer->id, new Quantity(1)),
        ]));

        $order = $orderRepository->get($orderId);

        self::assertCount(2, $order->lines);
        self::assertEquals(new Price(3 * 1500 + 1000), $order->total);
        self::assertSame((string) $order->id, (string) $orderRepository->get($order->id)->id);
    }

    public function testOrderKeepsThePriceItWasPlacedAt(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = self::getContainer()->get(OrderRepositoryInterface::class);

        $book = BookFactory::create(name: 'Dune', price: 1500);
        $bookRepository->add($book);

        $orderId = new OrderId();
        $commandBus->dispatch(new PlaceOrderCommand($orderId, [
            new OrderItem($book->id, new Quantity(2)),
        ]));

        $book->reprice(new Price(9999));
        $book->rename(new BookName('Dune (revised)'));

        $order = $orderRepository->get($orderId);
        $line = $order->lines[0] ?? false;
        self::assertNotFalse($line);
        self::assertEquals(new Price(1500), $line->unitPrice);
        self::assertEquals(new BookName('Dune'), $line->bookName);
        self::assertEquals(new Price(3000), $order->total);
    }

    public function testOrderSurvivesTheDisappearanceOfTheBook(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = self::getContainer()->get(OrderRepositoryInterface::class);

        $book = BookFactory::create(name: 'Dune', price: 1500);
        $bookRepository->add($book);

        $orderId = new OrderId();
        $commandBus->dispatch(new PlaceOrderCommand($orderId, [
            new OrderItem($book->id, new Quantity(1)),
        ]));

        $bookRepository->remove($book);

        $order = $orderRepository->get($orderId);
        $line = $order->lines[0] ?? false;
        self::assertNotFalse($line);
        self::assertEquals(new BookName('Dune'), $line->bookName);
        self::assertEquals(new Price(1500), $order->total);
    }

    public function testCannotOrderAMissingBook(): void
    {
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $this->expectException(MissingBookException::class);

        $commandBus->dispatch(new PlaceOrderCommand(new OrderId(), [
            new OrderItem(new BookId(), new Quantity(1)),
        ]));
    }
}
