<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\ValueObject\BookId;
use App\Shared\Application\Command\CommandInterface;
use Webmozart\Assert\Assert;

final class UpdateBookCommand implements CommandInterface
{
    public function __construct(
        public readonly BookId $id,
        public readonly ?string $name = null, // TODO VO
        public readonly ?string $description = null,
        public readonly ?string $author = null,
        public readonly ?string $content = null,
        public readonly ?int $price = null,
    ) {
        Assert::nullOrLengthBetween($name, 1, 255);
        Assert::nullOrLengthBetween($description, 1, 1023);
        Assert::nullOrLengthBetween($author, 1, 255);
        Assert::nullOrLengthBetween($content, 1, 65535);
        Assert::nullOrPositiveInteger($price);
    }
}
