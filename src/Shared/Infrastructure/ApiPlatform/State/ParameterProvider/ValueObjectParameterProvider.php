<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\State\ParameterProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\State\ParameterProviderInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Shared\Infrastructure\ApiPlatform\DomainPropertyType;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Turns a raw query/header parameter into a domain value object before it reaches a state provider.
 */
final readonly class ValueObjectParameterProvider implements ParameterProviderInterface
{
    public const string CLASS_KEY = 'value_object';

    public function provide(Parameter $parameter, array $parameters = [], array $context = []): ?Operation
    {
        if (!\is_string($value = $parameter->getValue())) {
            return null;
        }

        $class = $this->valueObjectClass($parameter, $context);
        if (null === $class) {
            return null;
        }

        try {
            $parameter->setValue(new $class($value));
        } catch (\InvalidArgumentException $e) {
            throw new ValidationException(new ConstraintViolationList([new ConstraintViolation($e->getMessage(), null, [], $value, $parameter->getKey(), $value)]));
        }

        return null;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return class-string|null
     */
    private function valueObjectClass(Parameter $parameter, array $context): ?string
    {
        /** @var class-string|null $declared */
        $declared = $parameter->getExtraProperties()[self::CLASS_KEY] ?? null;
        if (null !== $declared) {
            return $declared;
        }

        $operation = $context['operation'] ?? null;
        $property = $parameter->getProperty() ?? $parameter->getKey();

        if (!$operation instanceof Operation || null === $property) {
            return null;
        }

        /** @var class-string|null $resourceClass */
        $resourceClass = $operation->getClass();
        if (null === $resourceClass) {
            return null;
        }

        $class = DomainPropertyType::behind($resourceClass, $property);

        return null !== $class && DomainPropertyType::isBuildableFromString($class) ? $class : null;
    }
}
