<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Infrastructure\ApiPlatform\Resource\CategoryResource;
use App\Tests\BookStore\Factory\BookFactory;
use App\Tests\BookStore\Factory\CategoryFactory;
use Symfony\Component\Uid\Uuid;

final class BookCategoriesTest extends ApiTestCase
{
    public function testCreateCategory(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/categories', ['json' => ['name' => 'Science Fiction']]);

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(CategoryResource::class);
        self::assertJsonContains(['name' => 'Science Fiction']);
    }

    public function testClassifyBookUnderCategory(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        $category = CategoryFactory::create('Science Fiction');
        $categoryRepository->add($category);

        $response = $client->request('POST', \sprintf('/api/books/%s/categories', $book->id), [
            'json' => ['category' => \sprintf('/api/categories/%s', $category->id)],
        ]);

        self::assertResponseIsSuccessful();

        self::assertJsonContains([
            'classifications' => [
                ['category' => \sprintf('/api/categories/%s', $category->id)],
            ],
        ]);

        $classifications = $response->toArray()['classifications'];
        self::assertIsArray($classifications);
        self::assertIsArray($classifications[0]);
        self::assertIsString($classifications[0]['classifiedAt']);

        self::assertTrue($book->isClassifiedAs($category));
    }

    public function testDeclassifyBook(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);

        $book = BookFactory::create();
        $category = CategoryFactory::create('Science Fiction');
        $book->classify($category, new \DateTimeImmutable('2026-03-01 10:00:00'));
        $categoryRepository->add($category);
        $bookRepository->add($book);

        $client->request('DELETE', \sprintf('/api/books/%s/categories/%s', $book->id, $category->id));

        self::assertResponseIsSuccessful();
        self::assertFalse($book->isClassifiedAs($category));
    }

    public function testCannotClassifyUnderAMissingCategory(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        $client->request('POST', \sprintf('/api/books/%s/categories', $book->id), [
            'json' => ['category' => \sprintf('/api/categories/%s', Uuid::v4())],
        ]);

        self::assertResponseStatusCodeSame(404);
    }
}
