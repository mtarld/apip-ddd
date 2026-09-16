<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Output;

use App\BookStore\Domain\Model\OrderLine;
use App\BookStore\Infrastructure\ApiPlatform\ObjectMapper\BookIriTransform;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: OrderLine::class)]
final class OrderLineOutput
{
    #[Map(source: 'bookId', transform: BookIriTransform::class)]
    public ?string $book = null;

    public string $bookName;

    public int $unitPrice;

    public int $quantity;
}
