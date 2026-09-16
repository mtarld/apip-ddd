<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Exception;

use App\BookStore\Domain\ValueObject\OrderId;
use App\Shared\Domain\Exception\MissingModelException;

final class MissingOrderException extends MissingModelException
{
    public function __construct(OrderId $id, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('order', $id, $code, $previous);
    }
}
