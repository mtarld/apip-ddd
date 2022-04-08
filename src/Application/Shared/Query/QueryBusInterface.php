<?php

declare(strict_types=1);

namespace App\Application\Shared\Query;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): mixed;
}
