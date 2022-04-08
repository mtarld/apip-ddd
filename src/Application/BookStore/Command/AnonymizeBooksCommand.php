<?php

declare(strict_types=1);

namespace App\Application\BookStore\Command;

use App\Application\Shared\Command\CommandInterface;

final class AnonymizeBooksCommand implements CommandInterface
{
    public function __construct(
        public readonly string $anonymizedName,
    ) {
    }
}
