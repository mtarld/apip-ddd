<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\Discount;
use App\Shared\Application\Command\CommandInterface;

final class DiscountBookCommand implements CommandInterface
{
    public function __construct(
        public readonly BookId $id,
        public readonly Discount $discount,
    ) {
    }
}
