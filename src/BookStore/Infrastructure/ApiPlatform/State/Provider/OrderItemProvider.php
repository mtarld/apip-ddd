<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Domain\Model\Order;
use App\BookStore\Domain\Repository\OrderRepositoryInterface;
use App\BookStore\Domain\ValueObject\OrderId;
use Webmozart\Assert\Assert;

/**
 * @implements ProviderInterface<Order>
 */
final readonly class OrderItemProvider implements ProviderInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Order
    {
        Assert::isInstanceOf($uriVariables['id'], OrderId::class);

        return $this->orderRepository->get($uriVariables['id']);
    }
}
