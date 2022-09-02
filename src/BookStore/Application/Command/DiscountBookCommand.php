<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\Shared\Application\Command\CommandInterface;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final class DiscountBookCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $id,
        public readonly int $discountPercentage,
    ) {
        Assert::range($discountPercentage, 0, 100);
    }
}
