<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\BookStore\Domain\Model\Review;
use App\BookStore\Infrastructure\ApiPlatform\ObjectMapper\BookReferenceTransform;
use App\BookStore\Infrastructure\ApiPlatform\Payload\ReviewBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\ReviewBookProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\BookReviewCollectionProvider;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\BookReviewItemProvider;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ApiResource(
    shortName: 'Review',
    description: 'A reader\'s rating and comment on a book.',
    operations: [
        new GetCollection(
            uriTemplate: '/books/{bookId}/reviews',
            uriVariables: [
                'bookId' => new Link(fromClass: BookResource::class, toProperty: 'book'),
            ],
            provider: BookReviewCollectionProvider::class,
        ),
        new Get(
            uriTemplate: '/books/{bookId}/reviews/{id}',
            uriVariables: [
                'bookId' => new Link(fromClass: BookResource::class, toProperty: 'book'),
                'id' => new Link(fromClass: ReviewResource::class),
            ],
            provider: BookReviewItemProvider::class,
        ),
        new Post(
            uriTemplate: '/books/{bookId}/reviews',
            uriVariables: [
                'bookId' => new Link(fromClass: BookResource::class, toProperty: 'book'),
            ],
            input: ReviewBookPayload::class,
            processor: ReviewBookProcessor::class,
        ),
    ],
)]
#[Map(source: Review::class)]
final class ReviewResource
{
    #[ApiProperty(writable: false, identifier: true, jsonSchemaContext: ['type' => 'string', 'format' => 'uuid'])]
    public string $id;

    #[ApiProperty(writable: false)]
    #[Map(source: 'book', transform: BookReferenceTransform::class)]
    public BookResource $book;

    public int $rating;

    public string $comment;

    #[ApiProperty(writable: false)]
    public \DateTimeImmutable $writtenAt;
}
