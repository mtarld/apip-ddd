<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\AnonymizeAuthorBooksCommand;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Actor;
use App\BookStore\Domain\ValueObject\AnonymizationReason;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\Factory\AuthorFactory;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AnonymizeAuthorBooksTest extends KernelTestCase
{
    public function testTheAnonymousAuthorIsReusedAcrossRuns(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var BookViewFinder $books */
        $books = self::getContainer()->get(BookViewFinder::class);
        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $first = AuthorFactory::create('first');
        $second = AuthorFactory::create('second');
        $authorRepository->add($first);
        $authorRepository->add($second);
        $bookRepository->add(BookFactory::create(authorId: $first->id));
        $bookRepository->add(BookFactory::create(authorId: $second->id));

        $commandBus->dispatch(new AnonymizeAuthorBooksCommand(Actor::system('test'), $first->id, new AnonymizationReason('first request')));
        $anonymous = $authorRepository->findByName(AuthorName::anonymous());
        self::assertNotNull($anonymous);

        $commandBus->dispatch(new AnonymizeAuthorBooksCommand(Actor::system('test'), $second->id, new AnonymizationReason('second request')));

        self::assertSame((string) $anonymous->id, (string) $authorRepository->findByName(AuthorName::anonymous())?->id);
        self::assertCount(2, $books->byAuthor($anonymous->id));
    }
}
