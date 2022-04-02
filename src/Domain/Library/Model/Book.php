<?php

declare(strict_types=1);

namespace App\Domain\Library\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

#[ORM\Entity]
class Book
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    public readonly Uuid $id;

    public function __construct(
        #[ORM\Column(length: 255)]
        public string $name,

        #[ORM\Column(length: 1023)]
        public string $description,

        #[ORM\Column(length: 255)]
        public string $author,

        #[ORM\Column(length: 65535)]
        public string $content,

        #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
        public int $price,
    ) {
        $this->id = Uuid::v4();

        Assert::lengthBetween($name, 1, 255);
        Assert::lengthBetween($description, 1, 1023);
        Assert::lengthBetween($author, 1, 255);
        Assert::lengthBetween($content, 1, 65535);
        Assert::natural($price);
    }
}
