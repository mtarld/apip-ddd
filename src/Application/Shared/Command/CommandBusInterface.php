<?php

declare(strict_types=1);

namespace App\Application\Shared\Command;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): mixed;
}
