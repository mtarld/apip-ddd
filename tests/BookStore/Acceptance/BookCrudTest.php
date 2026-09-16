<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\ApiPlatform\Resource\BookResource;
use App\Tests\BookStore\Factory\AuthorFactory;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Component\Uid\Uuid;

final class BookCrudTest extends ApiTestCase
{
    public function testReturnPaginatedBooks(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        for ($i = 0; $i < 100; ++$i) {
            $bookRepository->add(BookFactory::create());
        }

        $client->request('GET', '/api/books');

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceCollectionJsonSchema(BookResource::class);

        self::assertJsonContains([
            'totalItems' => 100,
            'view' => [
                'first' => '/api/books?page=1',
                'next' => '/api/books?page=2',
                'last' => '/api/books?page=4',
            ],
        ]);
    }

    public function testFilterBooksByAuthor(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $authorOne = AuthorFactory::create('authorOne');
        $authorTwo = AuthorFactory::create('authorTwo');
        $authorRepository->add($authorOne);
        $authorRepository->add($authorTwo);

        $bookRepository->add(BookFactory::create(authorId: $authorOne->id));
        $bookRepository->add(BookFactory::create(authorId: $authorOne->id));
        $bookRepository->add(BookFactory::create(authorId: $authorTwo->id));

        $client->request('GET', \sprintf('/api/books?author=/api/authors/%s', $authorOne->id));

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceCollectionJsonSchema(BookResource::class);

        self::assertJsonContains([
            'member' => [
                ['author' => \sprintf('/api/authors/%s', $authorOne->id)],
                ['author' => \sprintf('/api/authors/%s', $authorOne->id)],
            ],
            'totalItems' => 2,
        ]);
    }

    public function testACollectionDoesNotShipEveryBooksFullText(): void
    {
        $client = self::createClient();
        $client->disableReboot();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create(content: 'the entire text of the book');
        $bookRepository->add($book);

        foreach (['/api/books', '/api/books/cheapest'] as $collection) {
            $members = $client->request('GET', $collection)->toArray()['member'];
            self::assertIsArray($members);

            $member = $members[0];
            self::assertIsArray($member);

            self::assertArrayNotHasKey('content', $member);

            self::assertSame('Book', $member['@type']);
            self::assertSame(\sprintf('/api/books/%s', $book->id), $member['@id']);
        }

        $full = $client->request('GET', \sprintf('/api/books/%s', $book->id))->toArray();
        self::assertSame('the entire text of the book', $full['content']);
    }

    public function testReturnBook(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $book = BookFactory::create(
            name: 'name',
            description: 'description',
            authorId: $author->id,
            content: 'content',
            price: 1000,
        );
        $bookRepository->add($book);

        $client->request('GET', \sprintf('/api/books/%s', (string) $book->id));

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(BookResource::class);

        self::assertJsonContains([
            'name' => 'name',
            'description' => 'description',
            'author' => \sprintf('/api/authors/%s', $author->id),
            'content' => 'content',
            'price' => 1000,
        ]);
    }

    public function testCreateBook(): void
    {
        $client = self::createClient();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $response = $client->request('POST', '/api/books', [
            'json' => [
                'isbn' => '9782123456803',
                'name' => 'name',
                'description' => 'description',
                'author' => \sprintf('/api/authors/%s', $author->id),
                'content' => 'content',
                'price' => 1000,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(BookResource::class);

        self::assertJsonContains([
            'name' => 'name',
            'description' => 'description',
            'author' => \sprintf('/api/authors/%s', $author->id),
            'content' => 'content',
            'price' => 1000,
        ]);

        $iri = $response->toArray()['@id'];
        self::assertIsString($iri);
        $id = new BookId(Uuid::fromString(\str_replace('/api/books/', '', $iri)));

        $book = self::getContainer()->get(BookRepositoryInterface::class)->get($id);

        self::assertEquals($id, $book->id);
        self::assertEquals(new BookName('name'), $book->name);
        self::assertEquals(new BookDescription('description'), $book->description);
        self::assertEquals($author->id, $book->authorId);
        self::assertEquals(new BookContent('content'), $book->content);
        self::assertEquals(new Price(1000), $book->price);
    }

    public function testCannotCreateBookWithoutValidPayload(): void
    {
        $client = self::createClient();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $client->request('POST', '/api/books', [
            'json' => [
                'isbn' => '9782123456803',
                'name' => '',
                'description' => '',
                'author' => \sprintf('/api/authors/%s', $author->id),
                'content' => '',
                'price' => -100,
            ],
        ]);

        self::assertResponseIsUnprocessable();
    }

    public function testAMalformedIdentifierIsNotFoundRatherThanBroken(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/books/not-a-uuid');

        self::assertResponseStatusCodeSame(404);
    }

    public function testCannotCreateBookWithAnInvalidIsbn(): void
    {
        $client = self::createClient();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $client->request('POST', '/api/books', [
            'json' => [
                'isbn' => '9782123456804',
                'name' => 'name',
                'description' => 'description',
                'author' => \sprintf('/api/authors/%s', $author->id),
                'content' => 'content',
                'price' => 1000,
            ],
        ]);

        self::assertResponseIsUnprocessable();
        self::assertJsonContains([
            'violations' => [
                ['propertyPath' => 'isbn', 'message' => 'ISBN check digit is invalid.'],
            ],
        ]);
    }

    public function testCannotCreateBookWithAMissingProperty(): void
    {
        $client = self::createClient();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $author = AuthorFactory::create();
        $authorRepository->add($author);

        $client->request('POST', '/api/books', [
            'json' => [
                'isbn' => '9782123456803',
                'description' => 'description',
                'author' => \sprintf('/api/authors/%s', $author->id),
                'content' => 'content',
                'price' => 1000,
            ],
        ]);

        self::assertResponseIsUnprocessable();
        self::assertJsonContains([
            'violations' => [
                ['propertyPath' => 'name', 'message' => 'This value should not be null.'],
            ],
        ]);
    }

    public function testAPartialPatchLeavesTheOtherFieldsAlone(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        $book = BookFactory::create(name: 'Dune', description: 'A desert planet');
        $bookRepository->add($book);

        $client->request('PATCH', \sprintf('/api/books/%s', $book->id), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['name' => 'Dune Messiah'],
        ]);

        self::assertResponseIsSuccessful();
        self::assertEquals(new BookName('Dune Messiah'), $bookRepository->get($book->id)->name);
        self::assertEquals(new BookDescription('A desert planet'), $bookRepository->get($book->id)->description);
    }

    public function testCannotCreateBookForAnAuthorThatDoesNotExist(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/books', [
            'json' => [
                'isbn' => '9782123456803',
                'name' => 'name',
                'description' => 'description',
                'author' => \sprintf('/api/authors/%s', Uuid::v4()),
                'content' => 'content',
                'price' => 1000,
            ],
        ]);

        self::assertResponseStatusCodeSame(404);
    }

    public function testCannotCreateBookWithALinkToSomethingElse(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        $client->request('POST', '/api/books', [
            'json' => [
                'isbn' => '9782123456803',
                'name' => 'name',
                'description' => 'description',
                'author' => \sprintf('/api/books/%s', $book->id),
                'content' => 'content',
                'price' => 1000,
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    public function testCannotCreateBookWithAMalformedAuthorLink(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/books', [
            'json' => [
                'isbn' => '9782123456803',
                'name' => 'name',
                'description' => 'description',
                'author' => 'not-an-iri',
                'content' => 'content',
                'price' => 1000,
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    public function testAmendBook(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        $book = BookFactory::create();
        $bookRepository->add($book);

        $client->request('PATCH', \sprintf('/api/books/%s', $book->id), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'isbn' => '9782123456803',
                'name' => 'newName',
                'description' => 'newDescription',
                'content' => 'newContent',
                'price' => 2000,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(BookResource::class);

        self::assertJsonContains([
            'name' => 'newName',
            'description' => 'newDescription',
            'content' => 'newContent',
            'price' => 2000,
        ]);

        $updatedBook = $bookRepository->get($book->id);

        self::assertEquals(new BookName('newName'), $updatedBook->name);
        self::assertEquals(new BookDescription('newDescription'), $updatedBook->description);
        self::assertEquals(new BookContent('newContent'), $updatedBook->content);
        self::assertEquals(new Price(2000), $updatedBook->price);
    }

    public function testAmendASingleField(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create(name: 'name', description: 'description');
        $bookRepository->add($book);

        $client->request('PATCH', \sprintf('/api/books/%s', $book->id), [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => [
                'isbn' => '9782123456803',
                'name' => 'newName',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(BookResource::class);

        self::assertJsonContains([
            'name' => 'newName',
        ]);

        $updatedBook = $bookRepository->get($book->id);

        self::assertEquals(new BookName('newName'), $updatedBook->name);
        self::assertEquals(new BookDescription('description'), $updatedBook->description);
    }

    public function testDeleteBook(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        $response = $client->request('DELETE', \sprintf('/api/books/%s', $book->id));

        self::assertResponseIsSuccessful();
        self::assertEmpty($response->getContent());

        $this->expectException(MissingBookException::class);
        $bookRepository->get($book->id);
    }
}
