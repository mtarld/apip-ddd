<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\OrderLineId;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Domain\ValueObject\Quantity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\AbstractUid;

#[ORM\Entity]
#[ORM\Table(name: '`order_line`')]
final class OrderLine
{
    public readonly OrderLineId $id;

    public BookId $bookId {
        get => new BookId($this->bookIdValue);
    }

    public Price $subtotal {
        get => $this->unitPrice->times($this->quantity);
    }

    #[ORM\Column(name: 'book_id', type: 'uuid')]
    private readonly AbstractUid $bookIdValue;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lineCollection')]
        #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        public readonly Order $order,

        BookId $bookId,

        public readonly BookName $bookName,

        public readonly Price $unitPrice,

        public readonly Quantity $quantity,
    ) {
        $this->id = new OrderLineId();
        $this->bookIdValue = $bookId->value;
    }
}
