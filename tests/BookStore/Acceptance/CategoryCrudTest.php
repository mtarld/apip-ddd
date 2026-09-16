<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Infrastructure\ApiPlatform\Resource\CategoryResource;
use App\Tests\BookStore\Factory\CategoryFactory;
use Symfony\Component\Uid\Uuid;

final class CategoryCrudTest extends ApiTestCase
{
    public function testListCategories(): void
    {
        $client = self::createClient();

        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);

        foreach (['Science Fiction', 'Fantasy', 'Essay'] as $name) {
            $categoryRepository->add(CategoryFactory::create($name));
        }

        $client->request('GET', '/api/categories');

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceCollectionJsonSchema(CategoryResource::class);
        self::assertJsonContains(['totalItems' => 3]);
    }

    public function testReturnCategory(): void
    {
        $client = self::createClient();

        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);

        $category = CategoryFactory::create('Science Fiction');
        $categoryRepository->add($category);

        $client->request('GET', \sprintf('/api/categories/%s', $category->id));

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(CategoryResource::class);
        self::assertJsonContains(['name' => 'Science Fiction']);
    }

    public function testAMissingCategoryIsNotFound(): void
    {
        $client = self::createClient();

        $client->request('GET', \sprintf('/api/categories/%s', Uuid::v4()));

        self::assertResponseStatusCodeSame(404);
    }
}
