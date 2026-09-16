<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Identifier;
use App\Shared\Domain\ValueObject\Identity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class CategoryId implements Identifier
{
    use Identity;
}
