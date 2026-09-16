<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Factory;

use App\BookStore\Domain\Model\Category;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\BookStore\Domain\ValueObject\CategoryName;

final class CategoryFactory
{
    private function __construct()
    {
    }

    public static function create(string $name = 'name'): Category
    {
        return new Category(new CategoryId(), new CategoryName($name));
    }
}
