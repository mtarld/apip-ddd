<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Isbn;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Application\Command\CommandInterface;

final readonly class CreateBookCommand implements CommandInterface
{
    public function __construct(
        public BookId $id,
        public Isbn $isbn,
        public BookName $name,
        public BookDescription $description,
        public AuthorId $authorId,
        public BookContent $content,
        public Price $price,
    ) {
    }
}
