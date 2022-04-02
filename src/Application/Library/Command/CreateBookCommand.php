<?php

declare(strict_types=1);

namespace App\Application\Library\Command;

use App\Domain\Shared\Command\CommandInterface;
use Webmozart\Assert\Assert;

final class CreateBookCommand implements CommandInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $author,
        public readonly string $content,
        public readonly int $price,
    ) {
        Assert::lengthBetween($name, 1, 255);
        Assert::lengthBetween($description, 1, 1023);
        Assert::lengthBetween($author, 1, 255);
        Assert::lengthBetween($content, 1, 65535);
        Assert::natural($price);
    }
}
