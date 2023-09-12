<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

use Formidable\Mapping\Formatter\Exception\InvalidTypeException;

use function is_string;

class NotEmptyConstraint implements ConstraintInterface
{
    public function __invoke(mixed $value): ValidationResult
    {
        if (! is_string($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'string');
        }

        if ($value === '') {
            return new ValidationResult(new ValidationError('error.empty'));
        }

        return new ValidationResult();
    }
}
