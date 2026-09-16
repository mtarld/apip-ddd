<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Doctrine;

use App\BookStore\Domain\Exception\MissingOrderException;
use App\BookStore\Domain\Model\Order;
use App\BookStore\Domain\Repository\OrderRepositoryInterface;
use App\BookStore\Domain\ValueObject\OrderId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(OrderRepositoryInterface::class, when: ['dev', 'prod'])]
final readonly class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[\Override]
    public function add(Order $order): void
    {
        $this->em->persist($order);
    }

    #[\Override]
    public function get(OrderId $id): Order
    {
        return $this->em->find(Order::class, $id->value) ?? throw new MissingOrderException($id);
    }
}
