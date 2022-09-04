<?php

declare(strict_types=1);

namespace App\Tests\Subscription;

use App\Subscription\Entity\Subscription;
use Symfony\Component\Uid\Uuid;

final class DummySubscriptionFactory
{
    private function __construct()
    {
    }

    public static function createSubscription(string $email = 'email@email.com'): Subscription
    {
        return new Subscription(Uuid::v4(), $email);
    }
}
