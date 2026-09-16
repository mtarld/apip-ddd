<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Exception;

use App\BookStore\Domain\ValueObject\CategoryId;
use App\Shared\Domain\Exception\MissingModelException;

final class MissingCategoryException extends MissingModelException
{
    public function __construct(CategoryId $id, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('category', $id, $code, $previous);
    }
}
