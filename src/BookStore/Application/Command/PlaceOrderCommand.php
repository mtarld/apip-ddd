<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\OrderId;
use App\BookStore\Domain\ValueObject\OrderItem;
use App\Shared\Application\Command\CommandInterface;
use Webmozart\Assert\Assert;

final readonly class PlaceOrderCommand implements CommandInterface
{
    /**
     * @param non-empty-list<OrderItem> $items
     */
    public function __construct(
        public OrderId $id,
        public array $items,
    ) {
        Assert::notEmpty($items, 'An order must contain at least one item.');
    }
}
