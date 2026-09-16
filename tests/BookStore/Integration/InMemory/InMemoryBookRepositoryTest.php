<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\InMemory;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Infrastructure\InMemory\InMemoryBookRepository;
use App\Tests\BookStore\Integration\BookRepositoryTestCase;

final class InMemoryBookRepositoryTest extends BookRepositoryTestCase
{
    protected function repository(): BookRepositoryInterface
    {
        return self::getContainer()->get(InMemoryBookRepository::class);
    }
}
