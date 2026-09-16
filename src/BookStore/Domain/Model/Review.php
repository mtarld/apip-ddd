<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\BookStore\Domain\ValueObject\ReviewRating;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`review`')]
final class Review
{
    public function __construct(
        public readonly ReviewId $id,

        #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'reviewCollection')]
        #[ORM\JoinColumn(name: 'book_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        public readonly Book $book,

        public readonly ReviewRating $rating,

        public readonly ReviewComment $comment,

        #[ORM\Column(name: 'written_at', type: 'datetime_immutable')]
        public readonly \DateTimeImmutable $writtenAt,
    ) {
    }
}
