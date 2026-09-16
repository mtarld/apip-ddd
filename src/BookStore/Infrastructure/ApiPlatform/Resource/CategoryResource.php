<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\BookStore\Domain\Model\Category;
use App\BookStore\Infrastructure\ApiPlatform\Payload\CreateCategoryPayload;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\CreateCategoryProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\CategoryCollectionProvider;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\CategoryItemProvider;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ApiResource(
    shortName: 'Category',
    description: 'A label a book can be classified under.',
    operations: [
        new GetCollection(
            provider: CategoryCollectionProvider::class,
        ),
        new Get(
            provider: CategoryItemProvider::class,
        ),
        new Post(
            input: CreateCategoryPayload::class,
            processor: CreateCategoryProcessor::class,
        ),
    ],
)]
#[Map(source: Category::class)]
final class CategoryResource
{
    #[ApiProperty(writable: false, identifier: true, jsonSchemaContext: ['type' => 'string', 'format' => 'uuid'])]
    public string $id;

    public string $name;
}
