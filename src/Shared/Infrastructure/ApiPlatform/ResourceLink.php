<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform;

/**
 * Marks a payload property as an IRI pointing at a resource, and says which one.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class ResourceLink
{
    /**
     * @param class-string $resource
     */
    public function __construct(
        public string $resource,
    ) {
    }
}
