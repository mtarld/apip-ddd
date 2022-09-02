<?php

declare(strict_types=1);

namespace App\BookStore\Application\Query;

use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\Uid\Uuid;

final class FindBookQuery implements QueryInterface
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
