<?php

declare(strict_types=1);

namespace Formidable\Exception;

use DomainException;

use function gettype;
use function sprintf;

final class InvalidValueException extends DomainException implements ExceptionInterface
{
    public static function fromArrayWithNonStringValues(array $array): self
    {
        return new self('Non-string value in array found');
    }

    public static function fromNonNestedValue(mixed $value): self
    {
        return new self(sprintf('Expected string or array value, but "%s" was provided', gettype($value)));
    }
}
