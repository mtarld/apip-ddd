<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BookStore\Domain\Model\Author;
use App\BookStore\Domain\Repository\AuthorRepositoryInterface;
use App\BookStore\Domain\ValueObject\AuthorId;
use Webmozart\Assert\Assert;

/**
 * @implements ProviderInterface<Author>
 */
final readonly class AuthorItemProvider implements ProviderInterface
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository,
    ) {
    }

    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Author
    {
        Assert::isInstanceOf($uriVariables['id'], AuthorId::class);

        return $this->authorRepository->get($uriVariables['id']);
    }
}
