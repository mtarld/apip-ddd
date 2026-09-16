<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class AnonymizationReason implements \Stringable
{
    public const int MIN_LENGTH = 1;
    public const int MAX_LENGTH = 1023;

    public function __construct(
        public string $value,
    ) {
        Assert::lengthBetween($value, self::MIN_LENGTH, self::MAX_LENGTH);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
