<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\UriVariable;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use App\Shared\Domain\ValueObject\Identifier;
use App\Shared\Infrastructure\ApiPlatform\DomainPropertyType;
use Symfony\Component\TypeInfo\Type;

/**
 * Tells the uri-variables converter that a resource's identifier is a domain identity.
 */
final readonly class IdentityPropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    public function __construct(
        private PropertyMetadataFactoryInterface $decorated,
    ) {
    }

    /**
     * @param class-string         $resourceClass
     * @param array<string, mixed> $options
     */
    public function create(string $resourceClass, string $property, array $options = []): ApiProperty
    {
        $metadata = $this->decorated->create($resourceClass, $property, $options);

        $identity = $this->identityBehind($resourceClass, $property);

        return null === $identity ? $metadata : $metadata->withNativeType(Type::object($identity));
    }

    /**
     * @param class-string $resourceClass
     *
     * @return class-string|null
     */
    private function identityBehind(string $resourceClass, string $property): ?string
    {
        $type = DomainPropertyType::behind($resourceClass, $property);

        return null !== $type && \is_a($type, Identifier::class, true) ? $type : null;
    }
}
