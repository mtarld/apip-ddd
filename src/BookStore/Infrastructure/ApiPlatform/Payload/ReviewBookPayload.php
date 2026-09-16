<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewRating;
use App\Shared\Infrastructure\Symfony\Validator\AssertValueObject;

final class ReviewBookPayload
{
    #[AssertValueObject(ReviewRating::class)]
    public int $rating;

    #[AssertValueObject(ReviewComment::class)]
    public string $comment;
}
