<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\ClassifyBookCommand;
use App\BookStore\Application\Command\DeclassifyBookCommand;
use App\BookStore\Domain\Exception\MissingCategoryException;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\Factory\BookFactory;
use App\Tests\BookStore\Factory\CategoryFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ClassifyBookTest extends KernelTestCase
{
    public function testClassifyAndDeclassifyBook(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        $category = CategoryFactory::create('Science Fiction');
        $categoryRepository->add($category);

        $commandBus->dispatch(new ClassifyBookCommand($book->id, $category->id));

        self::assertTrue($book->isClassifiedAs($category));
        self::assertCount(1, $book->categories);

        $commandBus->dispatch(new ClassifyBookCommand($book->id, $category->id));
        self::assertCount(1, $book->categories);

        $commandBus->dispatch(new DeclassifyBookCommand($book->id, $category->id));

        self::assertFalse($book->isClassifiedAs($category));
        self::assertCount(0, $book->categories);
    }

    public function testCannotClassifyUnderAMissingCategory(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);

        $book = BookFactory::create();
        $bookRepository->add($book);

        $this->expectException(MissingCategoryException::class);

        $commandBus->dispatch(new ClassifyBookCommand($book->id, new CategoryId()));
    }
}
