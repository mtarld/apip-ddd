<?php

declare(strict_types=1);

namespace App\Infrastructure\BookStore\ApiPlatform\OpenApi;

use ApiPlatform\Core\Api\FilterInterface;
use Symfony\Component\PropertyInfo\Type;

final class AuthorFilter implements FilterInterface
{
    public function getDescription(string $resourceClass): array
    {
        return [
            'author' => [
                'property' => 'author',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
            ],
        ];
    }
}
