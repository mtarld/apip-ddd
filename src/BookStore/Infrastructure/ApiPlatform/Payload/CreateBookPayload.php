<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Isbn;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\ApiPlatform\Resource\AuthorResource;
use App\Shared\Infrastructure\ApiPlatform\ResourceLink;
use App\Shared\Infrastructure\Symfony\Validator\AssertValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateBookPayload
{
    #[AssertValueObject(Isbn::class)]
    public string $isbn;

    #[AssertValueObject(BookName::class)]
    public string $name;

    #[AssertValueObject(BookDescription::class)]
    public string $description;

    #[ResourceLink(AuthorResource::class)]
    #[Assert\NotBlank]
    public string $author;

    #[AssertValueObject(BookContent::class)]
    public string $content;

    #[AssertValueObject(Price::class)]
    public int $price;
}
