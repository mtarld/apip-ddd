<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final readonly class ReviewRating
{
    public const int MIN = 1;
    public const int MAX = 5;

    public function __construct(
        #[ORM\Column(name: 'rating', type: 'smallint', options: ['unsigned' => true])]
        public int $value,
    ) {
        Assert::range($value, self::MIN, self::MAX);
    }
}
