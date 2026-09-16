<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final readonly class BookContent
{
    public const int MIN_LENGTH = 1;
    public const int MAX_LENGTH = 65535;

    public function __construct(
        #[ORM\Column(name: 'content', length: self::MAX_LENGTH)]
        public string $value,
    ) {
        Assert::lengthBetween($value, self::MIN_LENGTH, self::MAX_LENGTH);
    }

    public static function redacted(): self
    {
        return new self('redacted');
    }
}
