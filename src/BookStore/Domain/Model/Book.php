<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Model;

use App\BookStore\Domain\Event\BookPublished;
use App\BookStore\Domain\Event\RecordsEvents;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Discount;
use App\BookStore\Domain\ValueObject\Isbn;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Domain\ValueObject\ReviewComment;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\BookStore\Domain\ValueObject\ReviewRating;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\AbstractUid;

#[ORM\Entity]
#[ORM\Table(name: '`book`')]
final class Book
{
    use RecordsEvents;

    #[ORM\Version]
    #[ORM\Column(name: 'version', type: 'integer')]
    public private(set) int $version = 1;

    /**
     * @var list<Review>
     */
    public array $reviews {
        get => \array_values($this->reviewCollection->toArray());
    }

    /**
     * @var list<Classification>
     */
    public array $classifications {
        get => \array_values($this->classificationCollection->toArray());
    }

    /**
     * @var list<Category>
     */
    public array $categories {
        get => \array_map(static fn (Classification $classification): Category => $classification->category, $this->classifications);
    }

    public AuthorId $authorId {
        get => new AuthorId($this->authorIdValue);
    }

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'book', cascade: ['persist'], orphanRemoval: true, fetch: 'EXTRA_LAZY')]
    private Collection $reviewCollection;

    /**
     * @var Collection<int, Classification>
     */
    #[ORM\OneToMany(targetEntity: Classification::class, mappedBy: 'book', cascade: ['persist'], orphanRemoval: true)]
    private Collection $classificationCollection;

    #[ORM\Column(name: 'author_id', type: 'uuid')]
    private AbstractUid $authorIdValue;

    public function __construct(
        public readonly BookId $id,

        public readonly Isbn $isbn,

        public private(set) BookName $name,

        public private(set) BookDescription $description,

        AuthorId $authorId,

        public private(set) BookContent $content,

        public private(set) Price $price,
    ) {
        $this->authorIdValue = $authorId->value;
        $this->reviewCollection = new ArrayCollection();
        $this->classificationCollection = new ArrayCollection();

        $this->record(new BookPublished($this->id, $this->name, $authorId, $this->price));
    }

    public function rename(BookName $name): void
    {
        $this->name = $name;
    }

    public function describe(BookDescription $description): void
    {
        $this->description = $description;
    }

    public function reviseContent(BookContent $content): void
    {
        $this->content = $content;
    }

    public function reprice(Price $price): void
    {
        $this->price = $price;
    }

    public function anonymize(AuthorId $anonymousAuthorId): void
    {
        $this->name = BookName::redacted();
        $this->description = BookDescription::redacted();
        $this->content = BookContent::redacted();
        $this->authorIdValue = $anonymousAuthorId->value;
    }

    public function applyDiscount(Discount $discount): void
    {
        $this->price = $this->price->applyDiscount($discount);
    }

    public function review(ReviewId $id, ReviewRating $rating, ReviewComment $comment, \DateTimeImmutable $writtenAt): Review
    {
        $review = new Review($id, $this, $rating, $comment, $writtenAt);
        $this->reviewCollection->add($review);

        return $review;
    }

    public function classify(Category $category, \DateTimeImmutable $at): void
    {
        if (!$this->isClassifiedAs($category)) {
            $this->classificationCollection->add(new Classification($this, $category, $at));
        }
    }

    public function declassify(Category $category): void
    {
        if (($classification = $this->classificationUnder($category)) instanceof Classification) {
            $this->classificationCollection->removeElement($classification);
        }
    }

    public function isClassifiedAs(Category $category): bool
    {
        return $this->classificationUnder($category) instanceof Classification;
    }

    private function classificationUnder(Category $category): ?Classification
    {
        return $this->classificationCollection->findFirst(
            static fn (int $key, Classification $classification): bool => $classification->category->id->equals($category->id),
        );
    }
}
