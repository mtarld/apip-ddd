<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event\Integration;

use App\Shared\Domain\Event\EventInterface;

final readonly class BookPublished implements EventInterface
{
    public function __construct(
        public string $bookId,
        public string $title,
        public string $authorId,
        public int $price,
    ) {
    }
}
