<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\ObjectMapper;

use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<object, object>
 */
final readonly class ScalarTransform implements TransformCallableInterface
{
    #[\Override]
    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        if (!\is_object($value)) {
            return $value;
        }

        $scalar = \get_object_vars($value)['value'] ?? null;

        return $scalar instanceof \Stringable ? (string) $scalar : $scalar;
    }
}
