<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Application\Command\CommandInterface;

final readonly class UpdateBookCommand implements CommandInterface
{
    public function __construct(
        public BookId $id,
        public ?BookName $name = null,
        public ?BookDescription $description = null,
        public ?Author $author = null,
        public ?BookContent $content = null,
        public ?Price $price = null,
    ) {
    }
}
