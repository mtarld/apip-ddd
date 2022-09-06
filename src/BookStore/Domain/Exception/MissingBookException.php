<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Exception;

use App\BookStore\Domain\ValueObject\BookId;

final class MissingBookException extends \RuntimeException
{
    public function __construct(BookId $id, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Cannot find book with id %s', (string) $id), $code, $previous);
    }
}
