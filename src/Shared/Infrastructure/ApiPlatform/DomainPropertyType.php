<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform;

use Symfony\Component\ObjectMapper\Attribute\Map;

final readonly class DomainPropertyType
{
    private function __construct()
    {
    }

    /**
     * @param class-string $resourceClass
     *
     * @return class-string|null
     */
    public static function behind(string $resourceClass, string $property): ?string
    {
        if (!\class_exists($resourceClass)) {
            return null;
        }

        foreach (new \ReflectionClass($resourceClass)->getAttributes(Map::class) as $attribute) {
            $source = $attribute->newInstance()->source;

            if (!\is_string($source) || !\class_exists($source) || !\property_exists($source, $property)) {
                continue;
            }

            $type = new \ReflectionProperty($source, $property)->getType();
            if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
                continue;
            }

            /** @var class-string $name */
            $name = $type->getName();

            return $name;
        }

        return null;
    }

    /**
     * @param class-string $class
     */
    public static function isBuildableFromString(string $class): bool
    {
        $constructor = new \ReflectionClass($class)->getConstructor();
        if (null === $constructor || 1 > $constructor->getNumberOfParameters()) {
            return false;
        }

        $type = $constructor->getParameters()[0]->getType();
        $candidates = $type instanceof \ReflectionUnionType ? $type->getTypes() : [$type];

        return \array_any($candidates, static fn (?\ReflectionType $candidate): bool => $candidate instanceof \ReflectionNamedType && 'string' === $candidate->getName());
    }
}
