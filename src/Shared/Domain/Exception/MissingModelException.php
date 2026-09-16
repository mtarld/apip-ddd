<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

abstract class MissingModelException extends \RuntimeException
{
    public function __construct(string $what, \Stringable|string $id, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(\sprintf('Cannot find "%s" with id "%s"', $what, $id), $code, $previous);
    }
}
