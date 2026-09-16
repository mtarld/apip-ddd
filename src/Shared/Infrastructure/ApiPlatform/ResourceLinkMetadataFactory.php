<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

/**
 * Publishes `#[ResourceLink]` as `format: iri-reference`.
 */
#[AsDecorator('api_platform.metadata.property.metadata_factory.attribute')]
final readonly class ResourceLinkMetadataFactory implements PropertyMetadataFactoryInterface
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

        if (!(self::of($resourceClass, $property)) instanceof ResourceLink) {
            return $metadata;
        }

        return $metadata->withJsonSchemaContext(['type' => 'string', 'format' => 'iri-reference']);
    }

    /**
     * @param class-string $class
     */
    public static function of(string $class, string $property): ?ResourceLink
    {
        if (!\property_exists($class, $property)) {
            return null;
        }

        $attributes = new \ReflectionProperty($class, $property)->getAttributes(ResourceLink::class);

        return [] === $attributes ? null : $attributes[0]->newInstance();
    }
}
