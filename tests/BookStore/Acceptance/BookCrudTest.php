<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Author;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\Tests\BookStore\DummyFactory\DummyBookFactory;
use Symfony\Component\Uid\Uuid;

final class BookCrudTest extends ApiTestCase
{
    public function testReturnPaginatedBooks(): void
    {
        $client = static::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        for ($i = 0; $i < 100; ++$i) {
            $bookRepository->save(DummyBookFactory::createBook());
        }

        $client->request('GET', '/api/books');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(BookResource::class);

        static::assertJsonContains([
            'hydra:totalItems' => 100,
            'hydra:view' => [
                'hydra:first' => '/api/books?page=1',
                'hydra:next' => '/api/books?page=2',
                'hydra:last' => '/api/books?page=4',
            ],
        ]);
    }

    public function testFilterBooksByAuthor(): void
    {
        $client = static::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        $bookRepository->save(DummyBookFactory::createBook(author: 'authorOne'));
        $bookRepository->save(DummyBookFactory::createBook(author: 'authorOne'));
        $bookRepository->save(DummyBookFactory::createBook(author: 'authorTwo'));

        $client->request('GET', '/api/books?author=authorOne');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(BookResource::class);

        static::assertJsonContains([
            'hydra:member' => [
                ['author' => 'authorOne'],
                ['author' => 'authorOne'],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testReturnBook(): void
    {
        $client = static::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        $book = DummyBookFactory::createBook(
            name: 'name',
            description: 'description',
            author: 'author',
            content: 'content',
            price: 1000,
        );
        $bookRepository->save($book);

        $client->request('GET', sprintf('/api/books/%s', (string) $book->id()));

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(BookResource::class);

        static::assertJsonContains([
            'name' => 'name',
            'description' => 'description',
            'author' => 'author',
            'content' => 'content',
            'price' => 1000,
        ]);
    }

    public function testCreateBook(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/api/books', [
            'json' => [
                'name' => 'name',
                'description' => 'description',
                'author' => 'author',
                'content' => 'content',
                'price' => 1000,
            ],
        ]);

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(BookResource::class);

        static::assertJsonContains([
            'name' => 'name',
            'description' => 'description',
            'author' => 'author',
            'content' => 'content',
            'price' => 1000,
        ]);

        $id = new BookId(Uuid::fromString(str_replace('/api/books/', '', $response->toArray()['@id'])));

        /** @var Book $book */
        $book = static::getContainer()->get(BookRepositoryInterface::class)->ofId($id);

        static::assertNotNull($book);
        static::assertEquals($id, $book->id());
        static::assertEquals(new BookName('name'), $book->name());
        static::assertEquals(new BookDescription('description'), $book->description());
        static::assertEquals(new Author('author'), $book->author());
        static::assertEquals(new BookContent('content'), $book->content());
        static::assertEquals(new Price(1000), $book->price());
    }

    public function testCannotCreateBookWithoutValidPayload(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/books', [
            'json' => [
                'name' => '',
                'description' => '',
                'author' => '',
                'content' => '',
                'price' => -100,
            ],
        ]);

        static::assertResponseIsUnprocessable();
        static::assertJsonContains([
            'violations' => [
                ['propertyPath' => 'name', 'message' => 'This value is too short. It should have 1 character or more.'],
                ['propertyPath' => 'description', 'message' => 'This value is too short. It should have 1 character or more.'],
                ['propertyPath' => 'author', 'message' => 'This value is too short. It should have 1 character or more.'],
                ['propertyPath' => 'content', 'message' => 'This value is too short. It should have 1 character or more.'],
                ['propertyPath' => 'price', 'message' => 'This value should be either positive or zero.'],
            ],
        ]);

        $client->request('POST', '/api/books', [
            'json' => [],
        ]);

        static::assertResponseIsUnprocessable();
        static::assertJsonContains([
            'violations' => [
                ['propertyPath' => 'name', 'message' => 'This value should not be null.'],
                ['propertyPath' => 'description', 'message' => 'This value should not be null.'],
                ['propertyPath' => 'author', 'message' => 'This value should not be null.'],
                ['propertyPath' => 'content', 'message' => 'This value should not be null.'],
                ['propertyPath' => 'price', 'message' => 'This value should not be null.'],
            ],
        ]);
    }

    public function testUpdateBook(): void
    {
        $client = static::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        $book = DummyBookFactory::createBook();
        $bookRepository->save($book);

        $client->request('PUT', sprintf('/api/books/%s', $book->id()), [
            'json' => [
                'name' => 'newName',
                'description' => 'newDescription',
                'author' => 'newAuthor',
                'content' => 'newContent',
                'price' => 2000,
            ],
        ]);

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(BookResource::class);

        static::assertJsonContains([
            'name' => 'newName',
            'description' => 'newDescription',
            'author' => 'newAuthor',
            'content' => 'newContent',
            'price' => 2000,
        ]);

        $updatedBook = $bookRepository->ofId($book->id());

        static::assertNotNull($book);
        static::assertEquals(new BookName('newName'), $updatedBook->name());
        static::assertEquals(new BookDescription('newDescription'), $updatedBook->description());
        static::assertEquals(new Author('newAuthor'), $updatedBook->author());
        static::assertEquals(new BookContent('newContent'), $updatedBook->content());
        static::assertEquals(new Price(2000), $updatedBook->price());
    }

    public function testPartiallyUpdateBook(): void
    {
        $client = static::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        $book = DummyBookFactory::createBook(name: 'name', description: 'description');
        $bookRepository->save($book);

        $client->request('PATCH', sprintf('/api/books/%s', $book->id()), [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => [
                'name' => 'newName',
            ],
        ]);

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(BookResource::class);

        static::assertJsonContains([
            'name' => 'newName',
        ]);

        $updatedBook = $bookRepository->ofId($book->id());

        static::assertNotNull($book);
        static::assertEquals(new BookName('newName'), $updatedBook->name());
        static::assertEquals(new BookDescription('description'), $updatedBook->description());
    }

    public function testDeleteBook(): void
    {
        $client = static::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        $book = DummyBookFactory::createBook();
        $bookRepository->save($book);

        $response = $client->request('DELETE', sprintf('/api/books/%s', $book->id()));

        static::assertResponseIsSuccessful();
        static::assertEmpty($response->getContent());

        static::assertNull($bookRepository->ofId($book->id()));
    }
}
