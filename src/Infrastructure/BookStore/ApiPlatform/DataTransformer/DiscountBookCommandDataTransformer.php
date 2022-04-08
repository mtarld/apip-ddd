<?php

declare(strict_types=1);

namespace App\Infrastructure\BookStore\ApiPlatform\DataTransformer;

use ApiPlatform\DataTransformer\DataTransformerInterface;
use App\Application\BookStore\Command\DiscountBookCommand;
use App\Infrastructure\BookStore\ApiPlatform\Payload\DiscountBookPayload;
use App\Infrastructure\Shared\ApiPlatform\Metadata\CommandOperation;
use Symfony\Component\Uid\Uuid;

final class DiscountBookCommandDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = []): DiscountBookCommand
    {
        return new DiscountBookCommand(Uuid::fromString($context['identifiers_values']['id']), $object->discountPercentage);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return is_array($data)
            && DiscountBookPayload::class === $context['input']['class']
            && ($context['operation'] ?? null) instanceof CommandOperation
            && DiscountBookCommand::class === $context['operation']->getCommand()
        ;
    }
}
