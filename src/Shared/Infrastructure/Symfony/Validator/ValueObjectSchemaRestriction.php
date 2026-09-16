<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Validator;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Symfony\Validator\Metadata\Property\Restriction\PropertySchemaRestrictionMetadataInterface;
use App\Shared\Domain\ValueObject\Identifier;
use Symfony\Component\Validator\Constraint;

/**
 * Publishes a value object's bounds into the JSON Schema, so `#[AssertValueObject]` documents as well
 * as it validates.
 */
final readonly class ValueObjectSchemaRestriction implements PropertySchemaRestrictionMetadataInterface
{
    private const array KEYWORDS = [
        'minLength' => 'MIN_LENGTH',
        'maxLength' => 'MAX_LENGTH',
        'pattern' => 'PATTERN',
        'minimum' => 'MIN',
        'maximum' => 'MAX',
    ];

    public function supports(Constraint $constraint, ApiProperty $propertyMetadata): bool
    {
        return $constraint instanceof AssertValueObject;
    }

    /**
     * @return array<string, mixed>
     */
    public function create(Constraint $constraint, ApiProperty $propertyMetadata): array
    {
        if (!$constraint instanceof AssertValueObject) {
            return [];
        }

        $reflection = new \ReflectionClass($constraint->class);

        if ($reflection->implementsInterface(Identifier::class)) {
            return ['format' => 'uuid'];
        }

        $restriction = [];
        foreach (self::KEYWORDS as $keyword => $constantName) {
            if ($reflection->hasConstant($constantName)) {
                $restriction[$keyword] = $reflection->getConstant($constantName);
            }
        }

        return $restriction;
    }
}
