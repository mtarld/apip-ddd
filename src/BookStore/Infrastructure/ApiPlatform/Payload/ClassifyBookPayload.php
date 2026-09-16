<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use App\BookStore\Infrastructure\ApiPlatform\Resource\CategoryResource;
use App\Shared\Infrastructure\ApiPlatform\ResourceLink;
use Symfony\Component\Validator\Constraints as Assert;

final class ClassifyBookPayload
{
    #[ResourceLink(CategoryResource::class)]
    #[Assert\NotBlank]
    public string $category;
}
