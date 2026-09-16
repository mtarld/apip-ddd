<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class PlaceOrderPayload
{
    /**
     * @var list<OrderItemPayload>
     */
    #[Assert\Count(min: 1)]
    #[Assert\Valid]
    public array $items = [];
}
