<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration;

use App\BookStore\Domain\Exception\MissingCategoryException;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\BookStore\Domain\ValueObject\CategoryName;
use App\Shared\Domain\Repository\Pagination;
use App\Tests\BookStore\Factory\CategoryFactory;

abstract class CategoryRepositoryTestCase extends RepositoryTestCase
{
    public function testAddAndGet(): void
    {
        $repository = $this->repository();

        $category = CategoryFactory::create('Science Fiction');
        $repository->add($category);
        $this->persist();
        $this->detach();

        $found = $repository->get($category->id);

        self::assertSame((string) $category->id, (string) $found->id);
        self::assertEquals(new CategoryName('Science Fiction'), $found->name);
    }

    public function testGetRefusesAnUnknownIdentifier(): void
    {
        $this->expectException(MissingCategoryException::class);

        $this->repository()->get(new CategoryId());
    }

    public function testAll(): void
    {
        $repository = $this->repository();

        for ($i = 0; $i < 5; ++$i) {
            $repository->add(CategoryFactory::create('category'.$i));
        }
        $this->persist();

        self::assertCount(5, $repository->all());
    }

    public function testAllWithPagination(): void
    {
        $repository = $this->repository();

        for ($i = 0; $i < 5; ++$i) {
            $repository->add(CategoryFactory::create('category'.$i));
        }
        $this->persist();

        $collection = $repository->all(new Pagination(1, 2));

        self::assertCount(2, $collection);
        self::assertSame(5, $collection->totalItems);
        self::assertSame(3, $collection->lastPage);
    }

    abstract protected function repository(): CategoryRepositoryInterface;
}
