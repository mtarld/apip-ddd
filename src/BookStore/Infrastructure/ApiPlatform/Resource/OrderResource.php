<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\BookStore\Domain\Model\Order;
use App\BookStore\Infrastructure\ApiPlatform\Output\OrderLineOutput;
use App\BookStore\Infrastructure\ApiPlatform\Payload\PlaceOrderPayload;
use App\BookStore\Infrastructure\ApiPlatform\State\Processor\PlaceOrderProcessor;
use App\BookStore\Infrastructure\ApiPlatform\State\Provider\OrderItemProvider;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\ObjectMapper\Transform\MapCollection;

#[ApiResource(
    shortName: 'Order',
    description: 'A placed order, with the prices the books had at the time.',
    operations: [
        new Get(
            provider: OrderItemProvider::class,
        ),
        new Post(
            input: PlaceOrderPayload::class,
            processor: PlaceOrderProcessor::class,
        ),
    ],
)]
#[Map(source: Order::class)]
final class OrderResource
{
    #[ApiProperty(writable: false, identifier: true, jsonSchemaContext: ['type' => 'string', 'format' => 'uuid'])]
    public string $id;

    #[ApiProperty(writable: false)]
    public \DateTimeImmutable $placedAt;

    /**
     * @var list<OrderLineOutput>
     */
    #[ApiProperty(writable: false)]
    #[Map(source: 'lines', transform: new MapCollection(targetClass: OrderLineOutput::class))]
    public array $lines = [];

    #[ApiProperty(writable: false)]
    public int $total = 0;
}
