<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\BookStore;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Domain\BookStore\Model\Book;
use App\Domain\BookStore\Repository\BookRepositoryInterface;
use App\Infrastructure\BookStore\ApiPlatform\Resource\BookResource;

final class CheapestBooksTest extends ApiTestCase
{
    public function testReturnOnlyTheTenCheapestBooks(): void
    {
        $client = static::createClient();

        for ($i = 0; $i < 20; ++$i) {
            $book = new Book('name', 'description', 'author', 'content', $i);

            static::getContainer()->get(BookRepositoryInterface::class)->add($book);
        }

        $response = $client->request('GET', '/api/books/cheapest');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(BookResource::class);

        static::assertSame(10, $response->toArray()['hydra:totalItems']);

        $prices = [];
        for ($i = 0; $i < 10; ++$i) {
            $prices[] = ['price' => $i];
        }

        static::assertJsonContains(['hydra:member' => $prices]);
    }

    public function testReturnBooksSortedByPrice(): void
    {
        $client = static::createClient();

        $prices = [2000, 1000, 3000];
        array_walk($prices, static function (int $price): void {
            $book = new Book('name', 'description', 'author', 'content', $price);

            static::getContainer()->get(BookRepositoryInterface::class)->add($book);
        });

        $response = $client->request('GET', '/api/books/cheapest');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(BookResource::class);

        $responsePrices = array_map(fn (array $bookData): int => $bookData['price'], $response->toArray()['hydra:member']);
        static::assertSame([1000, 2000, 3000], $responsePrices);
    }
}
