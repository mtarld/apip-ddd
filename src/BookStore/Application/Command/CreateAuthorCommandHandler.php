<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Model\Author;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;

#[AsCommandHandler]
final readonly class CreateAuthorCommandHandler
{
    public function __construct(private AuthorRepositoryInterface $authorRepository)
    {
    }

    public function __invoke(CreateAuthorCommand $command): void
    {
        $this->authorRepository->add(new Author($command->id, $command->name));
    }
}
