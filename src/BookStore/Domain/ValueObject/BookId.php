<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class BookId implements \Stringable
{
    use AggregateRootId;
}
