<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Exception;

use App\BookStore\Domain\ValueObject\BookId;
use App\Shared\Domain\Exception\MissingModelException;

final class MissingBookException extends MissingModelException
{
    public function __construct(BookId $id, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('book', $id, $code, $previous);
    }
}
