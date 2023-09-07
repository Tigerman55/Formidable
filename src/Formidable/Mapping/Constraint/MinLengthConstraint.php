<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidLengthException;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;

use function iconv_strlen;
use function is_string;

final class MinLengthConstraint implements ConstraintInterface
{
    private int $lengthLimit;

    private string $encoding;

    public function __construct(int $lengthLimit, string $encoding = 'utf-8')
    {
        if ($lengthLimit < 0) {
            throw InvalidLengthException::fromNegativeLength($lengthLimit);
        }

        $this->lengthLimit = $lengthLimit;
        $this->encoding    = $encoding;
    }

    public function __invoke(mixed $value): ValidationResult
    {
        if (! is_string($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'string');
        }

        if (iconv_strlen($value, $this->encoding) < $this->lengthLimit) {
            return new ValidationResult(new ValidationError('error.min-length', ['lengthLimit' => $this->lengthLimit]));
        }

        return new ValidationResult();
    }
}
