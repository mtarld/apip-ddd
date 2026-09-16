<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\BookStore\Domain\ValueObject\ReviewRating;
use App\Shared\Application\Command\CommandInterface;

final readonly class ReviewBookCommand implements CommandInterface
{
    public function __construct(
        public BookId $bookId,
        public ReviewId $id,
        public ReviewRating $rating,
        public ReviewComment $comment,
    ) {
    }
}
