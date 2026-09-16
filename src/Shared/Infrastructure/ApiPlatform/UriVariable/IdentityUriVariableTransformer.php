<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\UriVariable;

use ApiPlatform\Metadata\Exception\InvalidUriVariableException;
use ApiPlatform\Metadata\UriVariableTransformerInterface;
use App\Shared\Domain\ValueObject\Identifier;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Turns `{id}` into the identity the domain speaks, before any provider sees it.
 */
final readonly class IdentityUriVariableTransformer implements UriVariableTransformerInterface
{
    public function supportsTransformation(mixed $value, array $types, array $context = []): bool
    {
        return \is_string($value) && isset($types[0]) && \is_a($types[0], Identifier::class, true);
    }

    public function transform(mixed $value, array $types, array $context = []): Identifier
    {
        Assert::string($value);

        /** @var class-string<Identifier> $class */
        $class = $types[0];

        try {
            return new $class(Uuid::fromString($value));
        } catch (\InvalidArgumentException $e) {
            throw new InvalidUriVariableException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
