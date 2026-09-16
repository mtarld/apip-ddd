<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Application\Command\AmendBookCommand;
use App\BookStore\Application\Command\ClassifyBookCommand;
use App\BookStore\Application\Command\DeclassifyBookCommand;
use App\BookStore\Application\Command\DeleteBookCommand;
use App\BookStore\Application\Command\DiscountBookCommand;
use App\BookStore\Domain\Exception\MissingBookException;
use App\BookStore\Domain\Exception\MissingCategoryException;
use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\BookStore\Domain\ValueObject\Discount;
use App\Shared\Application\Command\CommandBusInterface;
use App\Tests\BookStore\Factory\BookFactory;
use App\Tests\BookStore\Factory\CategoryFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class MissingModelTest extends KernelTestCase
{
    private CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);
    }

    public function testAmendingAMissingBook(): void
    {
        $this->expectException(MissingBookException::class);

        $this->commandBus->dispatch(new AmendBookCommand(new BookId(), name: new BookName('new')));
    }

    public function testDiscountingAMissingBook(): void
    {
        $this->expectException(MissingBookException::class);

        $this->commandBus->dispatch(new DiscountBookCommand(new BookId(), new Discount(20)));
    }

    public function testClassifyingAMissingBook(): void
    {
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);
        $category = CategoryFactory::create();
        $categoryRepository->add($category);

        $this->expectException(MissingBookException::class);

        $this->commandBus->dispatch(new ClassifyBookCommand(new BookId(), $category->id));
    }

    public function testDeclassifyingAMissingBook(): void
    {
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);
        $category = CategoryFactory::create();
        $categoryRepository->add($category);

        $this->expectException(MissingBookException::class);

        $this->commandBus->dispatch(new DeclassifyBookCommand(new BookId(), $category->id));
    }

    public function testDeclassifyingFromAMissingCategory(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);
        $book = BookFactory::create();
        $bookRepository->add($book);

        $this->expectException(MissingCategoryException::class);

        $this->commandBus->dispatch(new DeclassifyBookCommand($book->id, new CategoryId()));
    }

    public function testDeletingAMissingBook(): void
    {
        $this->expectException(MissingBookException::class);

        $this->commandBus->dispatch(new DeleteBookCommand(new BookId()));
    }
}
