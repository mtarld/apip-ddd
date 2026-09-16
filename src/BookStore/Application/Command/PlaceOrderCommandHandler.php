<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Model\Order;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\OrderRepositoryInterface;
use App\BookStore\Domain\ValueObject\PurchasedBook;
use App\Shared\Application\Command\AsCommandHandler;
use Psr\Clock\ClockInterface;

#[AsCommandHandler]
final readonly class PlaceOrderCommandHandler
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private OrderRepositoryInterface $orderRepository,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(PlaceOrderCommand $command): void
    {
        $purchases = [];
        foreach ($command->items as $item) {
            $book = $this->bookRepository->get($item->bookId);

            $purchases[] = new PurchasedBook($book->id, $book->name, $book->price, $item->quantity);
        }

        $this->orderRepository->add(Order::place($command->id, $this->clock->now(), ...$purchases));
    }
}
