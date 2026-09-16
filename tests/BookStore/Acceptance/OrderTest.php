<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Infrastructure\ApiPlatform\Resource\OrderResource;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Component\Uid\Uuid;

final class OrderTest extends ApiTestCase
{
    public function testPlaceAnOrder(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create(name: 'Dune', price: 1500);
        $bookRepository->add($book);

        $client->request('POST', '/api/orders', [
            'json' => [
                'items' => [
                    ['book' => \sprintf('/api/books/%s', $book->id), 'quantity' => 3],
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(OrderResource::class);

        self::assertJsonContains([
            'total' => 4500,
            'lines' => [
                [
                    'book' => \sprintf('/api/books/%s', $book->id),
                    'bookName' => 'Dune',
                    'unitPrice' => 1500,
                    'quantity' => 3,
                ],
            ],
        ]);
    }

    public function testOrderStillReadsAfterTheBookChanged(): void
    {
        $client = self::createClient();

        $client->disableReboot();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create(name: 'Dune', price: 1500);
        $bookRepository->add($book);

        $response = $client->request('POST', '/api/orders', [
            'json' => [
                'items' => [
                    ['book' => \sprintf('/api/books/%s', $book->id), 'quantity' => 2],
                ],
            ],
        ]);

        $orderIri = $response->toArray()['@id'];
        self::assertIsString($orderIri);

        $client->request('PATCH', \sprintf('/api/books/%s', $book->id), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['price' => 9999, 'name' => 'Dune (revised)'],
        ]);
        self::assertResponseIsSuccessful();

        $client->request('GET', $orderIri);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'total' => 3000,
            'lines' => [
                ['bookName' => 'Dune', 'unitPrice' => 1500],
            ],
        ]);
    }

    public function testAMissingOrderIsNotFound(): void
    {
        $client = self::createClient();

        $client->request('GET', \sprintf('/api/orders/%s', Uuid::v4()));

        self::assertResponseStatusCodeSame(404);
    }

    public function testCannotOrderAMissingBook(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/orders', [
            'json' => [
                'items' => [
                    ['book' => \sprintf('/api/books/%s', Uuid::v4()), 'quantity' => 1],
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(404);
    }

    public function testCannotPlaceAnEmptyOrder(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/orders', ['json' => ['items' => []]]);

        self::assertResponseIsUnprocessable();
    }
}
