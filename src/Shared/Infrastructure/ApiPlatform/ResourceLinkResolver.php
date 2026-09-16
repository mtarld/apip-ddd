<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform;

use ApiPlatform\Metadata\IriConverterInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Uid\Uuid;

/**
 * Turns the link a client sends into the identity the domain speaks.
 */
final readonly class ResourceLinkResolver
{
    public function __construct(
        private IriConverterInterface $iriConverter,
    ) {
    }

    public function identity(object $payload, string $property): Uuid
    {
        $link = ResourceLinkMetadataFactory::of($payload::class, $property)
            ?? throw new \LogicException(\sprintf('%s::$%s carries no #[ResourceLink].', $payload::class, $property));

        $expected = $link->resource;
        $iri = $payload->{$property};

        if (!\is_string($iri)) {
            throw new BadRequestHttpException(\sprintf('"%s" is not a valid link.', \get_debug_type($iri)));
        }

        try {
            $resource = $this->iriConverter->getResourceFromIri($iri);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException(\sprintf('"%s" is not a valid link.', $iri), $e);
        }

        if (!$resource instanceof $expected) {
            throw new BadRequestHttpException(\sprintf('"%s" does not point to a %s.', $iri, $this->shortName($expected)));
        }

        /** @var object{id: string} $resource */
        return Uuid::fromString($resource->id);
    }

    /**
     * @param class-string $class
     */
    private function shortName(string $class): string
    {
        return \lcfirst(\str_replace('Resource', '', new \ReflectionClass($class)->getShortName()));
    }
}
