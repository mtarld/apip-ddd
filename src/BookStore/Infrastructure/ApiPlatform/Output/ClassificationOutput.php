<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Output;

use App\BookStore\Infrastructure\ApiPlatform\ObjectMapper\CategoryIriTransform;
use App\BookStore\Infrastructure\ReadModel\ClassificationView;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: ClassificationView::class)]
final class ClassificationOutput
{
    #[Map(source: 'category', transform: CategoryIriTransform::class)]
    public ?string $category = null;

    public \DateTimeImmutable $classifiedAt;
}
