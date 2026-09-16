<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Exception;

use App\BookStore\Domain\ValueObject\AuthorId;
use App\Shared\Domain\Exception\MissingModelException;

final class MissingAuthorException extends MissingModelException
{
    public function __construct(AuthorId $id, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('author', $id, $code, $previous);
    }
}
