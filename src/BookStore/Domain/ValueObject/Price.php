<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final readonly class Price
{
    public const int MIN = 0;

    public function __construct(
        #[ORM\Column(name: 'price', type: 'integer', options: ['unsigned' => true])]
        public int $value,
    ) {
        Assert::greaterThanEq($value, self::MIN);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    #[\NoDiscard('the discounted price is a new instance, the original is left untouched')]
    public function applyDiscount(Discount $discount): self
    {
        $amountOff = $this->value * $discount->percentage / 100;

        return new self((int) \round($this->value - $amountOff));
    }

    public function times(Quantity $quantity): self
    {
        return new self($this->value * $quantity->value);
    }

    public function plus(self $other): self
    {
        return new self($this->value + $other->value);
    }
}
