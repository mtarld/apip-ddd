<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

final readonly class OrderItem
{
    public function __construct(
        public BookId $bookId,
        public Quantity $quantity,
    ) {
    }
}
