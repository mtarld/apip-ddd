<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\CategoryId;
use App\BookStore\Domain\ValueObject\CategoryName;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`category`')]
final class Category
{
    public function __construct(
        public readonly CategoryId $id,
        public readonly CategoryName $name,
    ) {
    }
}
