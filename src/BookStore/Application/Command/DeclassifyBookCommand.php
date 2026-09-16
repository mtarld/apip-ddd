<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\Shared\Application\Command\CommandInterface;

final readonly class DeclassifyBookCommand implements CommandInterface
{
    public function __construct(
        public BookId $bookId,
        public CategoryId $categoryId,
    ) {
    }
}
