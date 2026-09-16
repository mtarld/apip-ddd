<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\ObjectMapper;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\ObjectMapper\Metadata\Mapping;
use Symfony\Component\ObjectMapper\Metadata\ObjectMapperMetadataFactoryInterface;

/**
 * Unwraps value objects on their way from a domain model to an API resource.
 */
#[AsDecorator('object_mapper.metadata_factory', priority: -1000)]
final readonly class UnwrapValueObjectsMetadataFactory implements ObjectMapperMetadataFactoryInterface
{
    public function __construct(
        #[AutowireDecorated]
        private ObjectMapperMetadataFactoryInterface $decorated,
    ) {
    }

    public function create(object $object, ?string $property = null, array $context = []): array
    {
        $mappings = $this->decorated->create($object, $property, $context);

        if (null === $property || [] !== $mappings || !$this->isDomainModel($object)) {
            return $mappings;
        }

        if (!$this->holdsValueObject($object, $property)) {
            return $mappings;
        }

        return [new Mapping(target: $property, source: $property, transform: ScalarTransform::class)];
    }

    private function isDomainModel(object $object): bool
    {
        return \str_contains($object::class, '\\Domain\\Model\\');
    }

    private function holdsValueObject(object $object, string $property): bool
    {
        if (!\property_exists($object, $property)) {
            return false;
        }

        try {
            $value = new \ReflectionProperty($object, $property)->getValue($object);
        } catch (\Throwable) {
            return false;
        }

        return \is_object($value) && \array_key_exists('value', \get_object_vars($value));
    }
}
