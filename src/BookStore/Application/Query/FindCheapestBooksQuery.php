<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\Shared\Application\Query\QueryInterface;

final class FindCheapestBooksQuery implements QueryInterface
{
    public function __construct(public readonly int $size = 10)
    {
    }
}
