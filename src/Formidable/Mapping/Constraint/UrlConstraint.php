<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidTypeException;

use function filter_var;
use function is_string;

use const FILTER_VALIDATE_URL;

final class UrlConstraint implements ConstraintInterface
{
    public function __invoke(mixed $value): ValidationResult
    {
        if (! is_string($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'string');
        }

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return new ValidationResult(new ValidationError('error.url'));
        }

        return new ValidationResult();
    }
}
