<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\ObjectMapper;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Infrastructure\ApiPlatform\Resource\AuthorResource;
use Symfony\Component\ObjectMapper\TransformCallableInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @implements TransformCallableInterface<object, object>
 */
final readonly class AuthorIriTransform implements TransformCallableInterface
{
    public function __construct(
        private IriConverterInterface $iriConverter,
    ) {
    }

    #[\Override]
    public function __invoke(mixed $value, object $source, ?object $target): ?string
    {
        if (!$value instanceof AuthorId && (!\is_string($value) || !Uuid::isValid($value))) {
            return null;
        }

        return $this->iriConverter->getIriFromResource(
            AuthorResource::class,
            UrlGeneratorInterface::ABS_PATH,
            context: ['uri_variables' => ['id' => (string) $value]],
        );
    }
}
