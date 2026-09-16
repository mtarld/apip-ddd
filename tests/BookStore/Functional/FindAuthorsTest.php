<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Domain\Exception\MissingAuthorException;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\Shared\Domain\Repository\Pagination;
use App\Tests\BookStore\Factory\AuthorFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FindAuthorsTest extends KernelTestCase
{
    public function testFindAuthors(): void
    {
        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        foreach (['Herbert', 'Gibson', 'Stephenson'] as $name) {
            $authorRepository->add(AuthorFactory::create($name));
        }

        self::assertCount(3, $authorRepository->all());
        self::assertCount(2, $authorRepository->all(new Pagination(1, 2)));
    }

    public function testFindAuthorByName(): void
    {
        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $author = AuthorFactory::create('Herbert');
        $authorRepository->add($author);

        $found = $authorRepository->findByName(new AuthorName('Herbert'));
        self::assertNotNull($found);
        self::assertSame((string) $author->id, (string) $found->id);

        self::assertNull($authorRepository->findByName(new AuthorName('Nobody')));
    }

    public function testFindAuthorById(): void
    {
        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $this->expectException(MissingAuthorException::class);
        $authorRepository->get(new AuthorId());
    }
}
