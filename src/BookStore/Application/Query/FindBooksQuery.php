<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\BookStore\Domain\ValueObject\Author;
use App\Shared\Application\Query\QueryInterface;

final readonly class FindBooksQuery implements QueryInterface
{
    public function __construct(
        public ?Author $author = null,
        public ?int $page = null,
        public ?int $itemsPerPage = null,
    ) {
    }
}
