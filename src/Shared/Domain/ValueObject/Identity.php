<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

trait Identity
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid')]
    public readonly AbstractUid $value;

    final public function __construct(AbstractUid|string|null $value = null)
    {
        $this->value = match (true) {
            $value instanceof AbstractUid => $value,
            \is_string($value) => Uuid::fromString($value),
            default => Uuid::v4(),
        };
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value->equals($other->value);
    }
}
