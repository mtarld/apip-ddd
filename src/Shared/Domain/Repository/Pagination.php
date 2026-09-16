<?php

declare(strict_types=1);

namespace App\Shared\Domain\Repository;

use Webmozart\Assert\Assert;

final readonly class Pagination
{
    public const int DEFAULT_ITEMS_PER_PAGE = 100;

    public function __construct(
        public int $page,
        public int $itemsPerPage,
    ) {
        Assert::positiveInteger($page);
        Assert::positiveInteger($itemsPerPage);
    }

    public static function default(): self
    {
        return new self(1, self::DEFAULT_ITEMS_PER_PAGE);
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->itemsPerPage;
    }
}
