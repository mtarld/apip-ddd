<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\Shared\Application\Query\QueryInterface;

final class FindBooksQuery implements QueryInterface
{
    public function __construct(
        public readonly ?string $author = null,
        public readonly ?int $page = null,
        public readonly ?int $itemsPerPage = null,
    ) {
    }
}
