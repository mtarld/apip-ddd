<?php

declare(strict_types=1);

namespace App\Application\Library\Command;

use App\Domain\Shared\Command\CommandInterface;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final class UpdateBookCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $id,
        public readonly ?string $name = null,
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
