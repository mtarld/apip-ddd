<?php

declare(strict_types=1);

namespace App\Subscription\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    description: 'Someone who wants to hear about new books.',
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Delete(),
    ],
)]
#[ORM\Entity]
#[ORM\UniqueConstraint(fields: ['email'])]
#[UniqueEntity('email', message: 'This address is already subscribed.')]
class Subscription
{
    #[ApiProperty(writable: false)]
    #[ORM\Column(name: 'subscribed_at', type: \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)]
    public private(set) \DateTimeImmutable $subscribedAt;

    public function __construct(
        #[ApiProperty(readable: false, writable: false)]
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        #[ORM\GeneratedValue(strategy: 'CUSTOM')]
        #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
        public ?Uuid $id = null,

        #[Assert\NotBlank]
        #[Assert\Email]
        #[ORM\Column(nullable: false)]
        public ?string $email = null,
    ) {
        $this->subscribedAt = new \DateTimeImmutable();
    }
}
