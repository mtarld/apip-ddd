<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\InMemory;

use App\BookStore\Domain\Repository\OrderRepositoryInterface;
use App\BookStore\Infrastructure\InMemory\InMemoryOrderRepository;
use App\Tests\BookStore\Integration\OrderRepositoryTestCase;

final class InMemoryOrderRepositoryTest extends OrderRepositoryTestCase
{
    protected function repository(): OrderRepositoryInterface
    {
        return self::getContainer()->get(InMemoryOrderRepository::class);
    }
}
