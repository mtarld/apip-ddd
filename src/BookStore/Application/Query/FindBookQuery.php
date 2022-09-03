<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\BookStore\Domain\ValueObject\BookId;
use App\Shared\Application\Query\QueryInterface;

final class FindBookQuery implements QueryInterface
{
    public function __construct(
        public readonly BookId $id,
    ) {
    }
}
