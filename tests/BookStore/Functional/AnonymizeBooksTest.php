<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\AnonymizeBooksCommand;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\ValueObject\Author;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\DummyFactory\DummyBookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AnonymizeBooksTest extends KernelTestCase
{
    public function testAnonymizeAuthorOfBooks(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = static::getContainer()->get(BookRepositoryInterface::class);

        /** @var CommandBusInterface $commandBus */
        $commandBus = static::getContainer()->get(CommandBusInterface::class);

        for ($i = 0; $i < 10; ++$i) {
            $bookRepository->save(DummyBookFactory::createBook(author: sprintf('author_%d', $i)));
        }

        $commandBus->dispatch(new AnonymizeBooksCommand('anon.'));

        foreach ($bookRepository as $book) {
            self::assertEquals(new Author('anon.'), $book->author());
        }
    }
}
