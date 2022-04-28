<?php

declare(strict_types=1);

namespace App\Infrastructure\BookStore\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Application\BookStore\Command\AnonymizeBooksCommand;
use App\Domain\BookStore\Model\Book;
use App\Infrastructure\BookStore\ApiPlatform\OpenApi\AuthorFilter;
use App\Infrastructure\BookStore\ApiPlatform\Payload\DiscountBookPayload;
use App\Infrastructure\BookStore\ApiPlatform\State\Processor\AnonymizeBooksProcessor;
use App\Infrastructure\BookStore\ApiPlatform\State\Processor\BookCrudProcessor;
use App\Infrastructure\BookStore\ApiPlatform\State\Processor\DiscountBookProcessor;
use App\Infrastructure\BookStore\ApiPlatform\State\Provider\BookCrudProvider;
use App\Infrastructure\BookStore\ApiPlatform\State\Provider\CheapestBooksProvider;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Book',
    operations: [
        // queries
        new GetCollection(
            '/books/cheapest.{_format}',
            provider: CheapestBooksProvider::class,
            paginationEnabled: false,
            openapiContext: ['summary' => 'Find cheapest Book resources.'],
        ),

        // commands
        new Post(
            '/books/anonymize.{_format}',
            input: AnonymizeBooksCommand::class,
            processor: AnonymizeBooksProcessor::class,
            output: false,
            status: 202,
            openapiContext: ['summary' => 'Anonymize author of every Book resources.'],
        ),
        new Post(
            '/books/{id}/discount.{_format}',
            input: DiscountBookPayload::class,
            processor: DiscountBookProcessor::class,
            openapiContext: ['summary' => 'Apply a discount percentage on a Book resource.'],
        ),

        // basic crud
        new GetCollection(filters: [AuthorFilter::class], provider: BookCrudProvider::class),
        new Get(provider: BookCrudProvider::class),
        new Post(validationContext: ['groups' => ['create']], processor: BookCrudProcessor::class),
        new Put(processor: BookCrudProcessor::class),
        new Patch(processor: BookCrudProcessor::class),
        new Delete(processor: BookCrudProcessor::class),
    ],
)]
final class BookResource
{
    public function __construct(
        #[ApiProperty(identifier: true, writable: false)]
        public ?Uuid $id = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\Length(min: 1, max: 255, groups: ['create', 'Default'])]
        public ?string $name = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\Length(min: 1, max: 1023, groups: ['create', 'Default'])]
        public ?string $description = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\Length(min: 1, max: 255, groups: ['create', 'Default'])]
        public ?string $author = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\Length(min: 1, max: 65535, groups: ['create', 'Default'])]
        public ?string $content = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\PositiveOrZero(groups: ['create', 'Default'])]
        public ?int $price = null,
    ) {
        $this->id = $id ?? Uuid::v4();
    }

    public static function fromModel(Book $book): static
    {
        return new self($book->id, $book->name, $book->description, $book->author, $book->content, $book->price);
    }
}
