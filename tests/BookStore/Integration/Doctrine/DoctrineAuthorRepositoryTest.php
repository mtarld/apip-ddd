<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Integration\Doctrine;

use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Infrastructure\Doctrine\DoctrineAuthorRepository;
use App\Tests\BookStore\Integration\AuthorRepositoryTestCase;

final class DoctrineAuthorRepositoryTest extends AuthorRepositoryTestCase
{
    use RunsAgainstDoctrine;

    protected function repository(): AuthorRepositoryInterface
    {
        return self::getContainer()->get(DoctrineAuthorRepository::class);
    }

    /**
     * @return list<string>
     */
    protected static function tablesToTruncate(): array
    {
        return ['author'];
    }
}
