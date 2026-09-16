<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final readonly class Quantity
{
    public const int MIN = 1;
    public const int MAX = 1000;

    public function __construct(
        #[ORM\Column(name: 'quantity', type: 'smallint', options: ['unsigned' => true])]
        public int $value,
    ) {
        Assert::range($value, self::MIN, self::MAX);
    }
}
