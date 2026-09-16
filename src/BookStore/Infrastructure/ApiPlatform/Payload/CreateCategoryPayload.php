<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use App\BookStore\Domain\ValueObject\CategoryName;
use App\Shared\Infrastructure\Symfony\Validator\AssertValueObject;

final class CreateCategoryPayload
{
    #[AssertValueObject(CategoryName::class)]
    public string $name;
}
