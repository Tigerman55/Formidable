<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidTypeException;

use function filter_var;
use function is_string;

use const FILTER_VALIDATE_EMAIL;

final class EmailAddressConstraint implements ConstraintInterface
{
    public function __invoke(mixed $value): ValidationResult
    {
        if (! is_string($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'string');
        }

        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return new ValidationResult(new ValidationError('error.email-address'));
        }

        return new ValidationResult();
    }
}
