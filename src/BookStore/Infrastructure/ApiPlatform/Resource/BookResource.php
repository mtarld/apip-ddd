<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\McpTool;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\State\ParameterProvider\IriConverterParameterProvider;
use App\BookStore\Infrastructure\ApiPlatform\ObjectMapper\AuthorIriTransform;
use App\BookStore\Infrastructure\ApiPlatform\Output\BookListItemOutput;
use App\BookStore\Infrastructure\ApiPlatform\Output\ClassificationOutput;
use App\BookStore\Infrastructure\ApiPlatform\Payload\AmendBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\Payload\AnonymizeAuthorBooksPayload;
use App\BookStore\Infrastructure\ApiPlatform\Payload\ClassifyBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\Payload\CreateBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\Payload\DiscountBookMcpPayload;
use App\BookStore\Infrastructure\ApiPlatform\Payload\DiscountBookPayload;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\AmendBookProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\AnonymizeAuthorBooksProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\ClassifyBookProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\CreateBookProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\DeclassifyBookProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\DeleteBookProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\DiscountBookProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\BookCollectionProvider;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\BookItemProvider;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\CheapestBooksProvider;
use App\BookStore\Infrastructure\ReadModel\BookView;
use App\Shared\Infrastructure\ApiPlatform\ObjectMapper\ScalarTransform;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\ObjectMapper\Transform\MapCollection;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\TypeIdentifier;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Book',
    description: 'A book in the store.',
    operations: [
        new GetCollection(
            '/books/cheapest',
            openapi: new Operation(summary: 'Find cheapest Book resources.'),
            output: BookListItemOutput::class,
            itemUriTemplate: self::ITEM_URI_TEMPLATE,
            paginationEnabled: false,
            provider: CheapestBooksProvider::class,
            parameters: [
                'size' => new QueryParameter(
                    description: 'How many books to return.',
                    constraints: [new Assert\Range(min: 1, max: 100)],
                    nativeType: new BuiltinType(TypeIdentifier::INT),
                    castToNativeType: true,
                    default: 10,
                ),
            ],
        ),
        new Post(
            '/books/anonymize',
            status: 202,
            openapi: new Operation(summary: 'Anonymize author of every Book resources.'),
            input: AnonymizeAuthorBooksPayload::class,
            output: false,
            processor: AnonymizeAuthorBooksProcessor::class,
        ),
        new Post(
            '/books/{id}/categories',
            openapi: new Operation(summary: 'Classify a Book resource under a Category.'),
            input: ClassifyBookPayload::class,
            processor: ClassifyBookProcessor::class,
        ),
        new Delete(
            '/books/{id}/categories/{categoryId}',
            uriVariables: [
                'id' => new Link(fromClass: BookResource::class),
                'categoryId' => new Link(fromClass: CategoryResource::class, identifiers: ['id'], description: 'Category identifier'),
            ],
            openapi: new Operation(summary: 'Remove a Book resource from a Category.'),
            read: false,
            processor: DeclassifyBookProcessor::class,
        ),
        new Post(
            '/books/{id}/discount',
            openapi: new Operation(summary: 'Apply a discount percentage on a Book resource.'),
            input: DiscountBookPayload::class,
            provider: BookItemProvider::class,
            processor: DiscountBookProcessor::class,
        ),
        new GetCollection(
            output: BookListItemOutput::class,
            itemUriTemplate: self::ITEM_URI_TEMPLATE,
            provider: BookCollectionProvider::class,
            parameters: [
                'author' => new QueryParameter(
                    property: 'author',
                    description: 'Filter books by author, as an IRI.',
                    provider: IriConverterParameterProvider::class,
                ),
            ],
        ),
        new Get(
            jsonStream: true,
            provider: BookItemProvider::class,
        ),
        new Post(
            input: CreateBookPayload::class,
            processor: CreateBookProcessor::class,
        ),
        new Patch(
            input: AmendBookPayload::class,
            provider: BookItemProvider::class,
            processor: AmendBookProcessor::class,
        ),
        new Delete(
            provider: BookItemProvider::class,
            processor: DeleteBookProcessor::class,
        ),
    ],
    mcp: [
        'create_book' => new McpTool(
            description: 'Add a new book to the store. The author is an IRI, as returned by list_authors.',
            input: CreateBookPayload::class,
            processor: CreateBookProcessor::class,
            validate: true,
        ),
        'discount_book' => new McpTool(
            description: 'Apply a discount percentage to a book, given its id.',
            input: DiscountBookMcpPayload::class,
            processor: DiscountBookProcessor::class,
            validate: true,
        ),
        'anonymize_books' => new McpTool(
            description: 'Anonymize every book of an author. The author is an IRI.',
            input: AnonymizeAuthorBooksPayload::class,
            processor: AnonymizeAuthorBooksProcessor::class,
            validate: true,
        ),
    ],
)]
#[Map(source: BookView::class)]
final class BookResource
{
    public const string ITEM_URI_TEMPLATE = '/books/{id}{._format}';

    #[ApiProperty(writable: false, identifier: true, jsonSchemaContext: ['type' => 'string', 'format' => 'uuid'])]
    #[Map(source: 'id', transform: ScalarTransform::class)]
    public string $id;

    #[ApiProperty(writable: false)]
    public string $isbn;

    public string $name;

    public string $description;

    #[ApiProperty(writable: false)]
    #[Map(source: 'authorId', transform: AuthorIriTransform::class)]
    public string $author;

    public string $content;

    public int $price;

    #[ApiProperty(writable: false)]
    public ?float $averageRating = null;

    /**
     * @var list<ClassificationOutput>
     */
    #[ApiProperty(writable: false)]
    #[Map(source: 'classifications', transform: new MapCollection(targetClass: ClassificationOutput::class))]
    public array $classifications = [];
}
