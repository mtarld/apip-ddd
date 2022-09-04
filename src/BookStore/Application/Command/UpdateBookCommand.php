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

final class UpdateBookCommand implements CommandInterface
{
    public function __construct(
        public readonly BookId $id,
        public readonly ?BookName $name = null,
        public readonly ?BookDescription $description = null,
        public readonly ?Author $author = null,
        public readonly ?BookContent $content = null,
        public readonly ?Price $price = null,
    ) {
    }
}
