<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Price
{
    #[ORM\Column(name: 'price', type: 'integer', options: ['unsigned' => true])]
    public readonly int $amount;

    public function __construct(int $amount)
    {
        Assert::natural($amount);

        $this->amount = $amount;
    }

    public function applyDiscount(Discount $discount): static
    {
        $amount = $this->amount - ($this->amount * $discount->percentage / 100);

        return new static($amount);
    }
}
