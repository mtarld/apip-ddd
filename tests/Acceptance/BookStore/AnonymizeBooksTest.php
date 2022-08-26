<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\BookStore;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Domain\BookStore\Model\Book;
use App\Domain\BookStore\Repository\BookRepositoryInterface;

final class AnonymizeBooksTest extends ApiTestCase
{
    public function testAnonymizeAuthorOfBooks(): void
    {
        $client = static::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        for ($i = 0; $i < 10; ++$i) {
            $bookRepository->add(new Book('name', 'description', sprintf('author_%d', $i), 'content', 100));
        }

        $response = $client->request('POST', '/api/books/anonymize', [
            'json' => [
                'anonymizedName' => 'anon.',
            ],
        ]);

        static::assertResponseStatusCodeSame(202);
        static::assertEmpty($response->getContent());

        foreach ($bookRepository as $book) {
            self::assertSame('anon.', $book->author);
        }
    }
}
