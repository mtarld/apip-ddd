<?php

declare(strict_types=1);

namespace App\Application\BookStore\Query;

use App\Application\Shared\Query\QueryInterface;
use Symfony\Component\Uid\Uuid;

final class FindBookQuery implements QueryInterface
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
