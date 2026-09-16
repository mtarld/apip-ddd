<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\InMemory;

use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Infrastructure\InMemory\InMemoryAuthorRepository;
use App\Tests\BookStore\Integration\AuthorRepositoryTestCase;

final class InMemoryAuthorRepositoryTest extends AuthorRepositoryTestCase
{
    protected function repository(): AuthorRepositoryInterface
    {
        return self::getContainer()->get(InMemoryAuthorRepository::class);
    }
}
