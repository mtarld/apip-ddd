<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Output;

use App\BookStore\Infrastructure\ApiPlatform\ObjectMapper\AuthorIriTransform;
use App\BookStore\Infrastructure\ReadModel\BookView;
use App\Shared\Infrastructure\ApiPlatform\ObjectMapper\ScalarTransform;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\ObjectMapper\Transform\MapCollection;

#[Map(source: BookView::class)]
final class BookListItemOutput
{
    #[Map(source: 'id', transform: ScalarTransform::class)]
    public string $id;

    public string $isbn;

    public string $name;

    public string $description;

    #[Map(source: 'authorId', transform: AuthorIriTransform::class)]
    public string $author;

    public int $price;

    public ?float $averageRating = null;

    /**
     * @var list<ClassificationOutput>
     */
    #[Map(source: 'classifications', transform: new MapCollection(targetClass: ClassificationOutput::class))]
    public array $classifications = [];
}
