<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Actor implements \Stringable
{
    public const int MIN_LENGTH = 1;
    public const int MAX_LENGTH = 255;

    private function __construct(
        public string $value,
    ) {
        Assert::lengthBetween($value, self::MIN_LENGTH, self::MAX_LENGTH);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function system(string $process): self
    {
        return new self(\sprintf('system:%s', $process));
    }
}
