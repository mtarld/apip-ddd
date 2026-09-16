<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;

#[AsCommandHandler]
final readonly class RenameAuthorCommandHandler
{
    public function __construct(private AuthorRepositoryInterface $authorRepository)
    {
    }

    public function __invoke(RenameAuthorCommand $command): void
    {
        $author = $this->authorRepository->get($command->id);

        $author->rename($command->name);
    }
}
