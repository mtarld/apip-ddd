<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\Shared\Application\Command\CommandInterface;

final readonly class AnonymizeBooksCommand implements CommandInterface
{
    public function __construct(
        public string $anonymizedName,
    ) {
    }
}
