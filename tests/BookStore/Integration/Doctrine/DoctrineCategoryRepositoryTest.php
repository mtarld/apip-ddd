<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\Doctrine;

use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Infrastructure\Doctrine\DoctrineCategoryRepository;
use App\Tests\BookStore\Integration\CategoryRepositoryTestCase;

final class DoctrineCategoryRepositoryTest extends CategoryRepositoryTestCase
{
    use RunsAgainstDoctrine;

    protected function repository(): CategoryRepositoryInterface
    {
        return self::getContainer()->get(DoctrineCategoryRepository::class);
    }

    /**
     * @return list<string>
     */
    protected static function tablesToTruncate(): array
    {
        return ['category'];
    }
}
