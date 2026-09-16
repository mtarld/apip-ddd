<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\ValueObject\ClassificationId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`classification`')]
final class Classification
{
    public readonly ClassificationId $id;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'classificationCollection')]
        #[ORM\JoinColumn(name: 'book_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        public readonly Book $book,

        #[ORM\ManyToOne(targetEntity: Category::class)]
        #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false)]
        public readonly Category $category,

        #[ORM\Column(name: 'classified_at', type: 'datetime_immutable')]
        public readonly \DateTimeImmutable $classifiedAt,
    ) {
        $this->id = new ClassificationId();
    }
}
