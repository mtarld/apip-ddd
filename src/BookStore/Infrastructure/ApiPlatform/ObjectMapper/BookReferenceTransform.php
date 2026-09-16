<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\ObjectMapper;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @implements TransformCallableInterface<object, object>
 */
final readonly class BookReferenceTransform implements TransformCallableInterface
{
    #[\Override]
    public function __invoke(mixed $value, object $source, ?object $target): ?BookResource
    {
        if (!$value instanceof Book) {
            return null;
        }

        $reference = new BookResource();
        $reference->id = (string) $value->id;

        return $reference;
    }
}
