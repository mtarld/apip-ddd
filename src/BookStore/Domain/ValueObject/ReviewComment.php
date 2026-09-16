<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final readonly class ReviewComment
{
    public const int MIN_LENGTH = 1;
    public const int MAX_LENGTH = 1023;

    public function __construct(
        #[ORM\Column(name: 'comment', length: self::MAX_LENGTH)]
        public string $value,
    ) {
        Assert::lengthBetween($value, self::MIN_LENGTH, self::MAX_LENGTH);
    }
}
