<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Validator;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

/**
 * Makes `#[AssertValueObject]` on a non-nullable property mean "required" in the published schema.
 */
#[AsDecorator('api_platform.metadata.property.metadata_factory.validator')]
final readonly class ValueObjectRequiredMetadataFactory implements PropertyMetadataFactoryInterface
{
    public function __construct(
        #[AutowireDecorated]
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

        if ($metadata->isRequired() || !$this->isRequiredValueObject($resourceClass, $property)) {
            return $metadata;
        }

        return $metadata->withRequired(true);
    }

    /**
     * @param class-string $resourceClass
     */
    private function isRequiredValueObject(string $resourceClass, string $property): bool
    {
        if (!\property_exists($resourceClass, $property)) {
            return false;
        }

        $reflection = new \ReflectionProperty($resourceClass, $property);

        if ([] === $reflection->getAttributes(AssertValueObject::class)) {
            return false;
        }

        $type = $reflection->getType();

        return $type instanceof \ReflectionType && !$type->allowsNull() && !$reflection->hasDefaultValue();
    }
}
