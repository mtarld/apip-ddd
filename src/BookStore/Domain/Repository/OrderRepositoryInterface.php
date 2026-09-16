<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Repository;

use App\BookStore\Domain\Exception\MissingOrderException;
use App\BookStore\Domain\Model\Order;
use App\BookStore\Domain\ValueObject\OrderId;

interface OrderRepositoryInterface
{
    public function add(Order $order): void;

    /**
     * @throws MissingOrderException
     */
    public function get(OrderId $id): Order;
}
