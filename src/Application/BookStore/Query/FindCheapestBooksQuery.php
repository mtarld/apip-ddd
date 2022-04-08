<?php

declare(strict_types=1);

namespace App\Application\BookStore\Query;

use App\Application\Shared\Query\QueryInterface;

final class FindCheapestBooksQuery implements QueryInterface
{
    public function __construct(public readonly int $size = 10)
    {
    }
}
