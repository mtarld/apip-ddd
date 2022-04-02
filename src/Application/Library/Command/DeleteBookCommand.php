<?php

declare(strict_types=1);

namespace App\Application\Library\Command;

use App\Domain\Shared\Command\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class DeleteBookCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
