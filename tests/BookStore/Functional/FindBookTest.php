<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Functional;

use App\BookStore\Domain\Repository\BookRepositoryInterface;
use App\Tests\BookStore\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FindBookTest extends KernelTestCase
{
    public function testFindBookById(): void
    {
        /** @var BookRepositoryInterface $bookRepository */
        $bookRepository = self::getContainer()->get(BookRepositoryInterface::class);

        $book = BookFactory::create();
        $other = BookFactory::create();
        $bookRepository->add($book);
        $bookRepository->add($other);

        $found = $bookRepository->get($book->id);

        self::assertSame((string) $book->id, (string) $found->id);
    }
}
