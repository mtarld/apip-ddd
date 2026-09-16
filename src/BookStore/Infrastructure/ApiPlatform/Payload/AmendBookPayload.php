<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Infrastructure\Symfony\Validator\AssertValueObject;

final class AmendBookPayload
{
    #[AssertValueObject(BookName::class)]
    public ?string $name = null;

    #[AssertValueObject(BookDescription::class)]
    public ?string $description = null;

    #[AssertValueObject(BookContent::class)]
    public ?string $content = null;

    #[AssertValueObject(Price::class)]
    public ?int $price = null;
}
