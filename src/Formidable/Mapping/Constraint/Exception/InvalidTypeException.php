<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint\Exception;

use DomainException;

use function gettype;
use function is_object;
use function is_string;
use function sprintf;

final class InvalidTypeException extends DomainException implements ExceptionInterface
{
    public static function fromInvalidType(mixed $actualValue, string $expectedType): self
    {
        return new self(sprintf(
            'Value was expected to be of type %s, but got %s',
            $expectedType,
            is_object($actualValue) ? $actualValue::class : gettype($actualValue)
        ));
    }

    public static function fromNonNumericValue(mixed $actualValue): self
    {
        return new self(sprintf(
            'Value was expected to be numeric, but got %s',
            is_string($actualValue) ? sprintf('"%s"', $actualValue) : gettype($actualValue)
        ));
    }
}
