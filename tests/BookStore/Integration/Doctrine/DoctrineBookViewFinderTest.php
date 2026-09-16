<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\Doctrine;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookRepository;
use App\BookStore\Infrastructure\Doctrine\DoctrineBookViewFinder;
use App\BookStore\Infrastructure\Doctrine\DoctrineCategoryRepository;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Tests\BookStore\Integration\BookViewFinderTestCase;

final class DoctrineBookViewFinderTest extends BookViewFinderTestCase
{
    use RunsAgainstDoctrine;

    protected function finder(): BookViewFinder
    {
        return self::getContainer()->get(DoctrineBookViewFinder::class);
    }

    protected function books(): BookRepositoryInterface
    {
        return self::getContainer()->get(DoctrineBookRepository::class);
    }

    protected function categories(): CategoryRepositoryInterface
    {
        return self::getContainer()->get(DoctrineCategoryRepository::class);
    }

    /**
     * @return list<string>
     */
    protected static function tablesToTruncate(): array
    {
        return ['book', 'category'];
    }
}
