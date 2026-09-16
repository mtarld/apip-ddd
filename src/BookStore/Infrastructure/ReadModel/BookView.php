<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ReadModel;

use App\BookStore\Domain\ValueObject\BookId;

final readonly class BookView
{
    /**
     * @param list<ClassificationView> $classifications
     */
    public function __construct(
        public BookId $id,
        public string $isbn,
        public string $name,
        public string $description,
        public string $authorId,
        public string $content,
        public int $price,
        public ?float $averageRating,
        public array $classifications,
    ) {
    }
}
