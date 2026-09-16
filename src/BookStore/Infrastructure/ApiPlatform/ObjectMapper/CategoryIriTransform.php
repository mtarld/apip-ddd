<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\ObjectMapper;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use App\BookStore\Domain\Model\Category;
use App\BookStore\Infrastructure\ApiPlatform\Resource\CategoryResource;
use Symfony\Component\ObjectMapper\TransformCallableInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @implements TransformCallableInterface<object, object>
 */
final readonly class CategoryIriTransform implements TransformCallableInterface
{
    public function __construct(
        private IriConverterInterface $iriConverter,
    ) {
    }

    #[\Override]
    public function __invoke(mixed $value, object $source, ?object $target): ?string
    {
        $id = match (true) {
            $value instanceof Category => (string) $value->id,
            \is_string($value) && Uuid::isValid($value) => $value,
            default => null,
        };

        if (null === $id) {
            return null;
        }

        return $this->iriConverter->getIriFromResource(
            CategoryResource::class,
            UrlGeneratorInterface::ABS_PATH,
            context: ['uri_variables' => ['id' => $id]],
        );
    }
}
