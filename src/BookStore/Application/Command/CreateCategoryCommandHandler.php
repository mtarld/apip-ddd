<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command;

use App\BookStore\Domain\Model\Category;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;

#[AsCommandHandler]
final readonly class CreateCategoryCommandHandler
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function __invoke(CreateCategoryCommand $command): void
    {
        $this->categoryRepository->add(new Category($command->id, $command->name));
    }
}
