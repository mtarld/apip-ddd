<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\PlaceOrderCommand;
use App\BookStore\Domain\Repository\OrderRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\OrderId;
use App\BookStore\Domain\ValueObject\OrderItem;
use App\BookStore\Domain\ValueObject\Quantity;
use App\BookStore\Infrastructure\ApiPlatform\Payload\PlaceOrderPayload;
use App\BookStore\Infrastructure\ApiPlatform\Resource\OrderResource;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Infrastructure\ApiPlatform\ResourceLinkResolver;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<PlaceOrderPayload, OrderResource>
 */
final readonly class PlaceOrderProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ObjectMapperInterface $objectMapper,
        private OrderRepositoryInterface $orderRepository,
        private ResourceLinkResolver $links,
    ) {
    }

    #[\Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): OrderResource
    {
        Assert::isInstanceOf($data, PlaceOrderPayload::class);

        $items = [];
        foreach ($data->items as $item) {
            $items[] = new OrderItem(
                new BookId($this->links->identity($item, 'book')),
                new Quantity($item->quantity),
            );
        }

        Assert::notEmpty($items);

        $id = new OrderId();

        $this->commandBus->dispatch(new PlaceOrderCommand($id, $items));

        return $this->objectMapper->map($this->orderRepository->get($id), OrderResource::class);
    }
}
