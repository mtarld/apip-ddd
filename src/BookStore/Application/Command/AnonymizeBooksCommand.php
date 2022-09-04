<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\Shared\Application\Command\CommandInterface;

final class AnonymizeBooksCommand implements CommandInterface
{
    public function __construct(
        public readonly string $anonymizedName,
    ) {
    }
}
