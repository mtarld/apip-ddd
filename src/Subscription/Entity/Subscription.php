<?php

declare(strict_types=1);

namespace App\Subscription\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(),
        new Delete(),
    ],
)]
#[ORM\Entity]
class Subscription
{
    public function __construct(
        #[ApiProperty(readable: false, writable: false)]
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        #[ORM\GeneratedValue(strategy: 'CUSTOM')]
        #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
        public ?Uuid $id = null,

        #[Assert\NotBlank(groups: ['create'])]
        #[Assert\Email(groups: ['create', 'Default'])]
        #[ORM\Column(name: 'name', nullable: false)]
        public ?string $email = null,
    ) {
    }
}
