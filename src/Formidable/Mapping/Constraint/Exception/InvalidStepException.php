<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint\Exception;

use DomainException;
use Litipk\BigNumbers\Decimal;

use function sprintf;

final class InvalidStepException extends DomainException implements ExceptionInterface
{
    public static function fromNonNumericStep(string $step): self
    {
        return new self(sprintf('Step was expected to be numeric, but got "%s"', $step));
    }

    public static function fromNonNumericBase(string $base): self
    {
        return new self(sprintf('Base was expected to be numeric, but got "%s"', $base));
    }

    public static function fromZeroOrNegativeStep(Decimal $step): self
    {
        return new self(sprintf(
            'Step must be greater than zero, but got %s',
            (string) $step
        ));
    }
}
