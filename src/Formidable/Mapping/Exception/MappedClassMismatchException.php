<?php

declare(strict_types=1);

namespace Formidable\Mapping\Exception;

use DomainException;

use function gettype;
use function is_object;
use function sprintf;

final class MappedClassMismatchException extends DomainException implements ExceptionInterface
{
    public static function fromMismatchedClass(string $expectedClass, mixed $value): self
    {
        return new self(sprintf(
            'Value to bind or unbind must be an instance of %s, but got %s',
            $expectedClass,
            is_object($value) ? $value::class : gettype($value)
        ));
    }
}
