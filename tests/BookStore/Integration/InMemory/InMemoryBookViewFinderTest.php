<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\InMemory;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Infrastructure\InMemory\InMemoryBookRepository;
use App\BookStore\Infrastructure\InMemory\InMemoryBookViewFinder;
use App\BookStore\Infrastructure\InMemory\InMemoryCategoryRepository;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Tests\BookStore\Integration\BookViewFinderTestCase;

final class InMemoryBookViewFinderTest extends BookViewFinderTestCase
{
    protected function finder(): BookViewFinder
    {
        return self::getContainer()->get(InMemoryBookViewFinder::class);
    }

    protected function books(): BookRepositoryInterface
    {
        return self::getContainer()->get(InMemoryBookRepository::class);
    }

    protected function categories(): CategoryRepositoryInterface
    {
        return self::getContainer()->get(InMemoryCategoryRepository::class);
    }
}
