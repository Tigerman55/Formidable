<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidLimitException;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use Formidable\Mapping\Constraint\Exception\MissingDecimalDependencyException;
use Litipk\BigNumbers\Decimal;

use function class_exists;
use function is_numeric;

final class MinNumberConstraint implements ConstraintInterface
{
    private Decimal $limit;

    public function __construct(int|float|string $limit)
    {
        if (! class_exists(Decimal::class)) {
            // @codeCoverageIgnoreStart
            throw MissingDecimalDependencyException::fromMissingDependency();
            // @codeCoverageIgnoreEnd
        }

        if (! is_numeric($limit)) {
            throw InvalidLimitException::fromNonNumericValue($limit);
        }

        $this->limit = Decimal::fromString((string) $limit);
    }

    public function __invoke(mixed $value): ValidationResult
    {
        if (! is_numeric($value)) {
            throw InvalidTypeException::fromNonNumericValue($value);
        }

        $decimalValue = Decimal::fromString((string) $value);

        if ($decimalValue->comp($this->limit) === -1) {
            return new ValidationResult(new ValidationError('error.min-number', ['limit' => (string) $this->limit]));
        }

        return new ValidationResult();
    }
}
