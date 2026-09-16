<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\CategoryId;
use App\BookStore\Domain\ValueObject\CategoryName;
use App\Shared\Application\Command\CommandInterface;

final readonly class CreateCategoryCommand implements CommandInterface
{
    public function __construct(
        public CategoryId $id,
        public CategoryName $name,
    ) {
    }
}
