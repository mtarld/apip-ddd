<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Event;

use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Domain\Event\EventInterface;

final readonly class BookPublished implements EventInterface
{
    public function __construct(
        public BookId $bookId,
        public BookName $name,
        public AuthorId $authorId,
        public Price $price,
    ) {
    }
}
