<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class AssertValueObjectValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AssertValueObject) {
            throw new UnexpectedTypeException($constraint, AssertValueObject::class);
        }

        if (null === $value) {
            if ($this->propertyAllowsNull()) {
                return;
            }

            $this->context->buildViolation('This value should not be null.')
                ->setCode(NotNull::IS_NULL_ERROR)
                ->addViolation();

            return;
        }

        try {
            new ($constraint->class)($value);
        } catch (\InvalidArgumentException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', $e->getMessage())
                ->setInvalidValue($value)
                ->addViolation();
        }
    }

    private function propertyAllowsNull(): bool
    {
        $object = $this->context->getObject();
        $property = $this->context->getPropertyName();

        if (!\is_object($object) || null === $property || !\property_exists($object, $property)) {
            return true;
        }

        $type = new \ReflectionProperty($object, $property)->getType();

        return !$type instanceof \ReflectionType || $type->allowsNull();
    }
}
