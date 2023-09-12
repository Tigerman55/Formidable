<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidLengthException;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;

use function iconv_strlen;
use function is_string;

final class MaxLengthConstraint implements ConstraintInterface
{
    public function __construct(private readonly int $lengthLimit, private readonly string $encoding = 'utf-8')
    {
        if ($lengthLimit < 0) {
            throw InvalidLengthException::fromNegativeLength($lengthLimit);
        }
    }

    public function __invoke(mixed $value): ValidationResult
    {
        if (! is_string($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'string');
        }

        if (iconv_strlen($value, $this->encoding) > $this->lengthLimit) {
            return new ValidationResult(new ValidationError('error.max-length', ['lengthLimit' => $this->lengthLimit]));
        }

        return new ValidationResult();
    }
}
