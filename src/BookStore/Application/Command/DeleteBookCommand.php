<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\Shared\Application\Command\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class DeleteBookCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $id,
    ) {
    }
}
