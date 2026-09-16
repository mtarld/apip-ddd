<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Acceptance;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\BookStore\Domain\Event\BooksAnonymized;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Tests\BookStore\Factory\AuthorFactory;
use App\Tests\BookStore\Factory\BookFactory;
use App\Tests\Shared\Spy\EventBusSpy;

final class AnonymizeAuthorBooksTest extends ApiTestCase
{
    public function testAnonymizeBooksOfAuthor(): void
    {
        $client = self::createClient();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        /** @var BookViewFinder $books */
        $books = self::getContainer()->get(BookViewFinder::class);
        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);

        $target = AuthorFactory::create('targetAuthor');
        $other = AuthorFactory::create('otherAuthor');
        $authorRepository->add($target);
        $authorRepository->add($other);

        $bookRepository->add(BookFactory::create(description: 'secret', authorId: $target->id, content: 'sensitive'));
        $bookRepository->add(BookFactory::create(description: 'private', authorId: $target->id, content: 'confidential'));
        $bookRepository->add(BookFactory::create(description: 'public', authorId: $other->id, content: 'visible'));

        $client->request('POST', '/api/books/anonymize', [
            'json' => [
                'author' => \sprintf('/api/authors/%s', $target->id),
                'reason' => 'GDPR request',
            ],
        ]);

        self::assertResponseStatusCodeSame(202);

        $anonymous = $authorRepository->findByName(AuthorName::anonymous());
        self::assertNotNull($anonymous);

        $anonymizedBooks = $books->byAuthor($anonymous->id);
        self::assertCount(2, $anonymizedBooks);

        foreach ($anonymizedBooks as $view) {
            self::assertSame('redacted', $view->name);
            self::assertSame('redacted', $view->description);
            self::assertSame('redacted', $view->content);
        }

        $otherBooks = $books->byAuthor($other->id);
        self::assertCount(1, $otherBooks);

        foreach ($otherBooks as $view) {
            self::assertSame('public', $view->description);
            self::assertSame('visible', $view->content);
        }

        /** @var EventBusSpy $eventBus */
        $eventBus = self::getContainer()->get(EventBusSpy::class);
        $event = $eventBus->findDispatched(BooksAnonymized::class);

        self::assertNotNull($event);
        self::assertSame((string) $target->id, (string) $event->authorId);
        self::assertSame('GDPR request', (string) $event->reason);
        self::assertSame(2, $event->books);

        self::assertSame('system:api', (string) $event->actor);
    }
}
