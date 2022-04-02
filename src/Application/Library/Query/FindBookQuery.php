<?php

declare(strict_types=1);

namespace App\Application\Library\Query;

use App\Domain\Shared\Query\QueryInterface;
use Symfony\Component\Uid\Uuid;

final class FindBookQuery implements QueryInterface
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
