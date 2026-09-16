<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Discount
{
    public const int MIN = 0;
    public const int MAX = 100;

    public int $percentage;

    public function __construct(int $percentage)
    {
        Assert::range($percentage, self::MIN, self::MAX);

        $this->percentage = $percentage;
    }
}
