<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\BookStore\Application\Command\CreateCategoryCommand;
use App\BookStore\Domain\Repository\CategoryRepositoryInterface;
use App\BookStore\Domain\ValueObject\CategoryId;
use App\BookStore\Domain\ValueObject\CategoryName;
use App\BookStore\Infrastructure\ApiPlatform\Payload\CreateCategoryPayload;
use App\BookStore\Infrastructure\ApiPlatform\Resource\CategoryResource;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<CreateCategoryPayload, CategoryResource>
 */
final readonly class CreateCategoryProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ObjectMapperInterface $objectMapper,
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    #[\Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): CategoryResource
    {
        Assert::isInstanceOf($data, CreateCategoryPayload::class);

        $id = new CategoryId();

        $this->commandBus->dispatch(new CreateCategoryCommand($id, new CategoryName($data->name)));

        return $this->objectMapper->map($this->categoryRepository->get($id), CategoryResource::class);
    }
}
