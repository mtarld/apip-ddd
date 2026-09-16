<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\RenameAuthorCommand;
use App\BookStore\Domain\Exception\MissingAuthorException;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\AuthorName;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\Factory\AuthorFactory;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RenameAuthorTest extends KernelTestCase
{
    public function testRenameAuthorLeavesEveryBookAlone(): void
    {
        /** @var AuthorRepositoryInterface $authorRepository */
        $authorRepository = self::getContainer()->get(AuthorRepositoryInterface::class);
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        /** @var BookViewFinder $books */
        $books = self::getContainer()->get(BookViewFinder::class);
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $author = AuthorFactory::create('Frank Herbet');
        $authorRepository->add($author);
        $book = BookFactory::create(name: 'Dune', authorId: $author->id);
        $bookRepository->add($book);

        $commandBus->dispatch(new RenameAuthorCommand($author->id, new AuthorName('Frank Herbert')));

        self::assertEquals(new AuthorName('Frank Herbert'), $authorRepository->get($author->id)->name);

        self::assertSame((string) $author->id, (string) $bookRepository->get($book->id)->authorId);
        self::assertCount(1, $books->byAuthor($author->id));
    }

    public function testRenameAMissingAuthor(): void
    {
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $this->expectException(MissingAuthorException::class);

        $commandBus->dispatch(new RenameAuthorCommand(new AuthorId(), new AuthorName('Nobody')));
    }
}
