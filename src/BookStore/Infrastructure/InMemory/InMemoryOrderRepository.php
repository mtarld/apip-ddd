<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\InMemory;

use App\BookStore\Domain\Exception\MissingOrderException;
use App\BookStore\Domain\Model\Order;
use App\BookStore\Domain\Repository\OrderRepositoryInterface;
use App\BookStore\Domain\ValueObject\OrderId;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsAlias(OrderRepositoryInterface::class, public: true, when: ['test'])]
#[When('test')]
final class InMemoryOrderRepository implements OrderRepositoryInterface
{
    /** @var array<string, Order> */
    private array $orders = [];

    #[\Override]
    public function add(Order $order): void
    {
        $this->orders[(string) $order->id] = $order;
    }

    #[\Override]
    public function get(OrderId $id): Order
    {
        return $this->orders[(string) $id] ?? throw new MissingOrderException($id);
    }
}
