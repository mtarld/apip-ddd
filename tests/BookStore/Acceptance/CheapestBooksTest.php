<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\Tests\BookStore\Factory\BookFactory;

final class CheapestBooksTest extends ApiTestCase
{
    public function testReturnOnlyTheTenCheapestBooks(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        for ($i = 0; $i < 20; ++$i) {
            $bookRepository->add(BookFactory::create(price: $i));
        }

        $response = $client->request('GET', '/api/books/cheapest');

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceCollectionJsonSchema(BookResource::class);

        self::assertSame(10, $response->toArray()['totalItems']);

        $prices = [];
        for ($i = 0; $i < 10; ++$i) {
            $prices[] = ['price' => $i];
        }

        self::assertJsonContains(['member' => $prices]);
    }

    public function testReturnBooksSortedByPrice(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $prices = [2000, 1000, 3000];
        foreach ($prices as $price) {
            $bookRepository->add(BookFactory::create(price: $price));
        }

        $response = $client->request('GET', '/api/books/cheapest');

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceCollectionJsonSchema(BookResource::class);

        $member = $response->toArray()['member'];
        self::assertIsArray($member);
        $responsePrices = \array_map(static function (mixed $bookData): mixed {
            self::assertIsArray($bookData);

            return $bookData['price'];
        }, $member);
        self::assertSame([1000, 2000, 3000], $responsePrices);
    }

    public function testTheSizeParameterNarrowsTheResult(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        for ($i = 0; $i < 20; ++$i) {
            $bookRepository->add(BookFactory::create(price: $i));
        }

        $response = $client->request('GET', '/api/books/cheapest?size=3');

        self::assertResponseIsSuccessful();
        $member = $response->toArray()['member'];
        self::assertIsArray($member);
        self::assertCount(3, $member);
    }

    public function testTheSizeParameterIsValidated(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/books/cheapest?size=999');

        self::assertResponseIsUnprocessable();
    }
}
