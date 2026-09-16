<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\DeleteBookCommand;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Infrastructure\ReadModel\BookViewFinder;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DeleteBookTest extends KernelTestCase
{
    public function testDeleteBook(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        /** @var BookViewFinder $books */
        $books = self::getContainer()->get(BookViewFinder::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        self::assertCount(1, $books->all());

        $commandBus->dispatch(new DeleteBookCommand($book->id));

        self::assertCount(0, $books->all());
    }
}
