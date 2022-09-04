<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Domain\ValueObject\Price;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Book
{
    #[ORM\Embedded(columnPrefix: false)]
    public BookId $id;

    public function __construct(
        #[ORM\Embedded(columnPrefix: false)]
        public BookName $name,

        #[ORM\Embedded(columnPrefix: false)]
        public BookDescription $description,

        #[ORM\Embedded(columnPrefix: false)]
        public Author $author,

        #[ORM\Embedded(columnPrefix: false)]
        public BookContent $content,

        #[ORM\Embedded(columnPrefix: false)]
        public Price $price,
    ) {
        $this->id = new BookId();
    }

    public function applyDiscount(Discount $discount): static
    {
        $this->price = $this->price->applyDiscount($discount);

        return $this;
    }
}
