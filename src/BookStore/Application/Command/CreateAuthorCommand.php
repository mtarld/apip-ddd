<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\Shared\Application\Command\CommandInterface;

final readonly class CreateAuthorCommand implements CommandInterface
{
    public function __construct(
        public AuthorId $id,

        public AuthorName $name,
    ) {
    }
}
