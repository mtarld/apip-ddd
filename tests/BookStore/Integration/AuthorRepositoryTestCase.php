<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration;

use App\BookStore\Domain\Exception\MissingAuthorException;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\Shared\Domain\Repository\Pagination;
use App\Tests\BookStore\Factory\AuthorFactory;

abstract class AuthorRepositoryTestCase extends RepositoryTestCase
{
    public function testAddAndGet(): void
    {
        $repository = $this->repository();

        $author = AuthorFactory::create('Herbert');
        $repository->add($author);
        $this->persist();
        $this->detach();

        $found = $repository->get($author->id);

        self::assertSame((string) $author->id, (string) $found->id);
        self::assertEquals(new AuthorName('Herbert'), $found->name);
    }

    public function testGetRefusesAnUnknownIdentifier(): void
    {
        $this->expectException(MissingAuthorException::class);

        $this->repository()->get(new AuthorId());
    }

    public function testFindByName(): void
    {
        $repository = $this->repository();

        $repository->add(AuthorFactory::create('Herbert'));
        $this->persist();
        $this->detach();

        self::assertNotNull($repository->findByName(new AuthorName('Herbert')));
    }

    public function testFindByNameReturnsNullWhenNobodyCarriesIt(): void
    {
        self::assertNull($this->repository()->findByName(new AuthorName('Nobody')));
    }

    public function testAll(): void
    {
        $repository = $this->repository();

        for ($i = 0; $i < 5; ++$i) {
            $repository->add(AuthorFactory::create('author'.$i));
        }
        $this->persist();

        self::assertCount(5, $repository->all());
    }

    public function testAllWithPagination(): void
    {
        $repository = $this->repository();

        for ($i = 0; $i < 5; ++$i) {
            $repository->add(AuthorFactory::create('author'.$i));
        }
        $this->persist();

        $collection = $repository->all(new Pagination(1, 2));

        self::assertCount(2, $collection);
        self::assertSame(5, $collection->totalItems);
        self::assertSame(3, $collection->lastPage);
    }

    abstract protected function repository(): AuthorRepositoryInterface;
}
