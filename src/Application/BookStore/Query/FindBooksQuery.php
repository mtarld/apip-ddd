<?php

declare(strict_types=1);

namespace App\Application\BookStore\Query;

use App\Application\Shared\Query\QueryInterface;

final class FindBooksQuery implements QueryInterface
{
    public function __construct(
        public readonly ?string $author = null,
        public readonly ?int $page = null,
        public readonly ?int $itemsPerPage = null,
    ) {
    }
}
