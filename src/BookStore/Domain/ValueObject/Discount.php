<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Discount
{
    public readonly int $percentage;

    public function __construct(int $percentage)
    {
        Assert::range($percentage, 0, 100);

        $this->percentage = $percentage;
    }
}
