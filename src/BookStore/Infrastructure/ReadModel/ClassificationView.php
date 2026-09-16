<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ReadModel;

final readonly class ClassificationView
{
    public function __construct(
        public string $category,
        public \DateTimeImmutable $classifiedAt,
    ) {
    }
}
