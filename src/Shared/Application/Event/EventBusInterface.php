<?php

declare(strict_types=1);

namespace App\Shared\Application\Event;

use App\Shared\Domain\Event\EventInterface;

interface EventBusInterface
{
    public function dispatch(EventInterface $event): void;
}
