<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\BookStore\Domain\Model\Author;
use App\BookStore\Infrastructure\ApiPlatform\Payload\CreateAuthorPayload;
use App\BookStore\Infrastructure\ApiPlatform\Payload\RenameAuthorPayload;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\CreateAuthorProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\RenameAuthorProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\AuthorCollectionProvider;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\AuthorItemProvider;
use App\Shared\Infrastructure\ApiPlatform\State\ParameterProvider\ValueObjectParameterProvider;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ApiResource(
    shortName: 'Author',
    description: 'An author whose books the store sells.',
    operations: [
        new GetCollection(
            provider: AuthorCollectionProvider::class,
            parameters: [
                'name' => new QueryParameter(
                    property: 'name',
                    description: 'Find the author with this exact name.',
                    provider: ValueObjectParameterProvider::class,
                ),
            ],
        ),
        new Get(
            provider: AuthorItemProvider::class,
        ),
        new Post(
            input: CreateAuthorPayload::class,
            processor: CreateAuthorProcessor::class,
        ),
        new Patch(
            input: RenameAuthorPayload::class,
            provider: AuthorItemProvider::class,
            processor: RenameAuthorProcessor::class,
        ),
    ],
)]
#[Map(source: Author::class)]
final class AuthorResource
{
    #[ApiProperty(writable: false, identifier: true, jsonSchemaContext: ['type' => 'string', 'format' => 'uuid'])]
    public string $id;

    public string $name;
}
