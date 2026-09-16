<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final readonly class Isbn implements \Stringable
{
    public const string PATTERN = '^\\d{13}$';
    public const int MIN_LENGTH = 13;
    public const int MAX_LENGTH = 13;

    public function __construct(
        #[ORM\Column(name: 'isbn', length: self::MAX_LENGTH)]
        public string $value,
    ) {
        Assert::regex($value, '/'.self::PATTERN.'/', 'An ISBN-13 is thirteen digits. Got: %s');
        Assert::true($this->checkDigitIsValid($value), 'ISBN check digit is invalid.');
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function checkDigitIsValid(string $digits): bool
    {
        $sum = 0;
        for ($i = 0; $i < 12; ++$i) {
            $sum += (int) $digits[$i] * (0 === $i % 2 ? 1 : 3);
        }

        return (10 - $sum % 10) % 10 === (int) $digits[12];
    }
}
