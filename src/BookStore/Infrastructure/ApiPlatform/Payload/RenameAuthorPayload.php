<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use App\BookStore\Domain\ValueObject\AuthorName;
use App\Shared\Infrastructure\Symfony\Validator\AssertValueObject;

final class RenameAuthorPayload
{
    #[AssertValueObject(AuthorName::class)]
    public string $name;
}
