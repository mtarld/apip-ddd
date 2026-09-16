<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\InMemory;

use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Infrastructure\InMemory\InMemoryCategoryRepository;
use App\Tests\BookStore\Integration\CategoryRepositoryTestCase;

final class InMemoryCategoryRepositoryTest extends CategoryRepositoryTestCase
{
    protected function repository(): CategoryRepositoryInterface
    {
        return self::getContainer()->get(InMemoryCategoryRepository::class);
    }
}
