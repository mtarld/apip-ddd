<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Infrastructure\ApiPlatform\Resource\AuthorResource;
use App\Tests\BookStore\Factory\AuthorFactory;
use Symfony\Component\Uid\Uuid;

final class AuthorCrudTest extends ApiTestCase
{
    public function testCreateAuthor(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/authors', ['json' => ['name' => 'Frank Herbert']]);

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceItemJsonSchema(AuthorResource::class);
        self::assertJsonContains(['name' => 'Frank Herbert']);
    }

    public function testListAuthors(): void
    {
        $client = self::createClient();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        foreach (['Herbert', 'Gibson', 'Le Guin'] as $name) {
            $authorRepository->add(AuthorFactory::create($name));
        }

        $client->request('GET', '/api/authors');

        self::assertResponseIsSuccessful();
        self::assertMatchesResourceCollectionJsonSchema(AuthorResource::class);
        self::assertJsonContains(['totalItems' => 3]);
    }

    public function testRenameAuthor(): void
    {
        $client = self::createClient();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $author = AuthorFactory::create('Frank Herbet');
        $authorRepository->add($author);

        $client->request('PATCH', \sprintf('/api/authors/%s', $author->id), [
            'json' => ['name' => 'Frank Herbert'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['name' => 'Frank Herbert']);
    }

    public function testAMissingAuthorIsNotFound(): void
    {
        $client = self::createClient();

        $client->request('GET', \sprintf('/api/authors/%s', Uuid::v4()));

        self::assertResponseStatusCodeSame(404);
    }

    public function testFilterAuthorsByName(): void
    {
        $client = self::createClient();

        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $authorRepository->add(AuthorFactory::create('Frank Herbert'));
        $authorRepository->add(AuthorFactory::create('William Gibson'));

        $client->request('GET', '/api/authors?name=Frank Herbert');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'totalItems' => 1,
            'member' => [['name' => 'Frank Herbert']],
        ]);
    }

    public function testTheValueObjectIsTheParameterConstraint(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/authors?name='.\str_repeat('a', 300));

        self::assertResponseIsUnprocessable();
    }
}
