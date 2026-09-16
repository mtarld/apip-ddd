<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Exception;

use App\BookStore\Domain\ValueObject\BookId;
use App\BookStore\Domain\ValueObject\ReviewId;
use App\Shared\Domain\Exception\MissingModelException;

final class MissingReviewException extends MissingModelException
{
    public function __construct(BookId $bookId, ReviewId $id, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('review', \sprintf('%s on book %s', $id, $bookId), $code, $previous);
    }
}
