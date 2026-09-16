<?php

declare(strict_types=1);

namespace App\Tests\Subscription;

use App\Subscription\Entity\Subscription;

final class SubscriptionFactory
{
    private function __construct()
    {
    }

    public static function create(string $email = 'email@email.com'): Subscription
    {
        return new Subscription(email: $email);
    }
}
