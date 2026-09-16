<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Infrastructure\ApiPlatform\Resource\AuthorResource;
use App\Shared\Infrastructure\ApiPlatform\ResourceLink;
use App\Shared\Infrastructure\Symfony\Validator\AssertValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final class AnonymizeAuthorBooksPayload
{
    #[ResourceLink(AuthorResource::class)]
    #[Assert\NotBlank]
    public string $author;

    #[AssertValueObject(AnonymizationReason::class)]
    public string $reason;
}
