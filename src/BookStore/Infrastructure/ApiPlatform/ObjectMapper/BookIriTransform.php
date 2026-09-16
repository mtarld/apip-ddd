<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\ObjectMapper;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<object, object>
 */
final readonly class BookIriTransform implements TransformCallableInterface
{
    public function __construct(
        private IriConverterInterface $iriConverter,
    ) {
    }

    #[\Override]
    public function __invoke(mixed $value, object $source, ?object $target): ?string
    {
        if (!$value instanceof BookId) {
            return null;
        }

        return $this->iriConverter->getIriFromResource(
            BookResource::class,
            UrlGeneratorInterface::ABS_PATH,
            context: ['uri_variables' => ['id' => (string) $value]],
        );
    }
}
