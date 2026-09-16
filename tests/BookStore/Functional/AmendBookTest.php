<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\AmendBookCommand;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Price;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AmendBookTest extends KernelTestCase
{
    public function testAmendBook(): void
    {
        $authorId = new AuthorId();

        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $initialBook = BookFactory::create(
            name: 'name',
            description: 'description',
            authorId: $authorId,
            content: 'content',
            price: 1000,
        );

        $bookRepository->add($initialBook);

        $commandBus->dispatch(new AmendBookCommand(
            $initialBook->id,
            name: new BookName('newName'),
            content: new BookContent('newContent'),
            price: new Price(2000),
        ));

        $book = $bookRepository->get($initialBook->id);
        self::assertInstanceOf(\App\BookStore\Domain\Model\Book::class, $book);

        self::assertEquals(new BookName('newName'), $book->name);
        self::assertEquals(new BookDescription('description'), $book->description);
        self::assertEquals($authorId, $book->authorId);
        self::assertEquals(new BookContent('newContent'), $book->content);
        self::assertEquals(new Price(2000), $book->price);
    }
}
