<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Factory;

use App\BookStore\Domain\Model\Author;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;

final class AuthorFactory
{
    private function __construct()
    {
    }

    public static function create(string $name = 'name'): Author
    {
        return new Author(new AuthorId(), new AuthorName($name));
    }
}
