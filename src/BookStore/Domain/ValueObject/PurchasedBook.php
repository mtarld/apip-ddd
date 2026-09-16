<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

final readonly class PurchasedBook
{
    public function __construct(
        public BookId $bookId,
        public BookName $bookName,
        public Price $unitPrice,
        public Quantity $quantity,
    ) {
    }
}
