<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class AssertValueObject extends Constraint
{
    public string $message = '{{ error }}';

    /**
     * @param class-string $class
     * @param ?string[]    $groups
     */
    public function __construct(
        public string $class,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
