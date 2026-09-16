<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use App\BookStore\Domain\ValueObject\Quantity;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\Shared\Infrastructure\ApiPlatform\ResourceLink;
use App\Shared\Infrastructure\Symfony\Validator\AssertValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderItemPayload
{
    #[ResourceLink(BookResource::class)]
    #[Assert\NotBlank]
    public string $book;

    #[AssertValueObject(Quantity::class)]
    public int $quantity = 1;
}
