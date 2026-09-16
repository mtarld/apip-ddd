<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\Discount;
use App\Shared\Infrastructure\Symfony\Validator\AssertValueObject;

final class DiscountBookMcpPayload
{
    #[AssertValueObject(BookId::class)]
    public string $id;

    #[AssertValueObject(Discount::class)]
    public int $discountPercentage;
}
