<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Application\Command\CommandInterface;

final readonly class CreateBookCommand implements CommandInterface
{
    public function __construct(
        public BookName $name,
        public BookDescription $description,
        public Author $author,
        public BookContent $content,
        public Price $price,
    ) {
    }
}
