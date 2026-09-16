<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\OrderId;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Domain\ValueObject\PurchasedBook;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Table(name: '`order`')]
final class Order
{
    /**
     * @var list<OrderLine>
     */
    public array $lines {
        get => \array_values($this->lineCollection->toArray());
    }

    public Price $total {
        get {
            $total = Price::zero();
            foreach ($this->lineCollection as $line) {
                $total = $total->plus($line->subtotal);
            }

            return $total;
        }
    }

    /**
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'order', cascade: ['persist'], orphanRemoval: true)]
    private Collection $lineCollection;

    private function __construct(
        public readonly OrderId $id,

        #[ORM\Column(name: 'placed_at', type: 'datetime_immutable')]
        public readonly \DateTimeImmutable $placedAt,
    ) {
        $this->lineCollection = new ArrayCollection();
    }

    public static function place(OrderId $id, \DateTimeImmutable $placedAt, PurchasedBook ...$purchases): self
    {
        Assert::notEmpty($purchases, 'An order must contain at least one item.');

        $order = new self($id, $placedAt);
        foreach ($purchases as $purchase) {
            $order->lineCollection->add(new OrderLine(
                $order,
                $purchase->bookId,
                $purchase->bookName,
                $purchase->unitPrice,
                $purchase->quantity,
            ));
        }

        return $order;
    }
}
