<?php

declare(strict_types=1);

namespace App\Tests\BookStore\Factory;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Domain\ValueObject\AuthorId;
use App\BookStore\Domain\ValueObject\BookContent;
use App\BookStore\Domain\ValueObject\BookDescription;
use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\BookName;
use App\BookStore\Domain\ValueObject\Isbn;
use App\BookStore\Domain\ValueObject\Price;

final class BookFactory
{
    private function __construct()
    {
    }

    public static function create(
        string $name = 'name',
        string $description = 'description',
        ?AuthorId $authorId = null,
        string $content = 'content',
        int $price = 1000,
    ): Book {
        return new Book(
            new BookId(),
            self::anIsbn(),
            new BookName($name),
            new BookDescription($description),
            $authorId ?? new AuthorId(),
            new BookContent($content),
            new Price($price),
        );
    }

    public static function anIsbn(): Isbn
    {
        $digits = '978'.\str_pad((string) \random_int(0, 999_999_999), 9, '0', \STR_PAD_LEFT);

        $sum = 0;
        for ($i = 0; $i < 12; ++$i) {
            $sum += (int) $digits[$i] * (0 === $i % 2 ? 1 : 3);
        }

        return new Isbn($digits.(10 - $sum % 10) % 10);
    }
}
