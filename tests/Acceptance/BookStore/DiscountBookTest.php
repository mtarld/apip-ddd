<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\BookStore;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Domain\BookStore\Model\Book;
use App\Domain\BookStore\Repository\BookRepositoryInterface;
use App\Infrastructure\BookStore\ApiPlatform\Resource\BookResource;

final class DiscountBookTest extends ApiTestCase
{
    public function testApplyADiscountOnBook(): void
    {
        $client = static::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        $book = new Book('name', 'description', 'author', 'content', 1000);
        $bookRepository->add($book);

        $client->request('POST', sprintf('/api/books/%s/discount', (string) $book->id), [
            'json' => [
                'discountPercentage' => 20,
            ],
        ]);

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(BookResource::class);
        static::assertJsonContains(['price' => 800]);

        static::assertSame(800, $bookRepository->ofId($book->id)->price);
    }

    public function testValidateDiscountAmount(): void
    {
        $client = static::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        $book = new Book('name', 'description', 'author', 'content', 1000);
        $bookRepository->add($book);

        $client->request('POST', sprintf('/api/books/%s/discount', (string) $book->id), [
            'json' => [
                'discountPercentage' => 200,
            ],
        ]);

        static::assertResponseIsUnprocessable();
        static::assertJsonContains([
            'violations' => [
                ['propertyPath' => 'discountPercentage', 'message' => 'This value should be between 0 and 100.'],
            ],
        ]);

        static::assertSame(1000, $bookRepository->ofId($book->id)->price);
    }
}
