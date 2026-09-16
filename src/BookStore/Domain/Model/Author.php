<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`author`')]
final class Author
{
    public function __construct(
        public readonly AuthorId $id,
        public private(set) AuthorName $name,
    ) {
    }

    public function rename(AuthorName $name): void
    {
        $this->name = $name;
    }
}
