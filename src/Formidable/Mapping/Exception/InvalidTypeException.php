<?php

declare(strict_types=1);

namespace Formidable\Mapping\Exception;

use InvalidArgumentException;

use function gettype;
use function is_object;
use function sprintf;

final class InvalidTypeException extends InvalidArgumentException implements ExceptionInterface
{
    public static function fromInvalidType(mixed $actualValue, string $expectedType): self
    {
        return new self(sprintf(
            'Value was expected to be of type %s, but got %s',
            $expectedType,
            is_object($actualValue) ? $actualValue::class : gettype($actualValue)
        ));
    }
}
