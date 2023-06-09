<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\Shared\Application\Query\QueryInterface;

final readonly class FindCheapestBooksQuery implements QueryInterface
{
    public function __construct(public int $size = 10)
    {
    }
}
