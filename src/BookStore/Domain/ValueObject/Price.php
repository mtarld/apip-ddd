<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Price
{
    #[ORM\Column(name: 'price', type: 'integer', options: ['unsigned' => true])]
    public readonly int $value;

    public function __construct(int $value)
    {
        Assert::natural($value);

        $this->value = $value;
    }
}
