<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\CreateBookCommand;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\BookStore\Infrastructure\ReadModel\BookView;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CreateBookTest extends KernelTestCase
{
    public function testCreateBook(): void
    {
        $authorId = new AuthorId();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        /** @var BookViewFinder $books */
        $books = self::getContainer()->get(BookViewFinder::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        self::assertCount(0, $books->all());

        $commandBus->dispatch(new CreateBookCommand(
            new BookId(),
            BookFactory::anIsbn(),
            new BookName('name'),
            new BookDescription('description'),
            $authorId,
            new BookContent('content'),
            new Price(1000),
        ));

        self::assertCount(1, $books->all());

        $view = \array_first(\iterator_to_array($books->all()));
        self::assertInstanceOf(BookView::class, $view);

        $book = $bookRepository->get($view->id);

        self::assertEquals(new BookName('name'), $book->name);
        self::assertEquals(new BookDescription('description'), $book->description);
        self::assertEquals($authorId, $book->authorId);
        self::assertEquals(new BookContent('content'), $book->content);
        self::assertEquals(new Price(1000), $book->price);
    }
}
