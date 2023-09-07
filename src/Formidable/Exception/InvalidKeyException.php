<?php

declare(strict_types=1);

namespace Formidable\Exception;

use DomainException;

use function gettype;
use function sprintf;

final class InvalidKeyException extends DomainException implements ExceptionInterface
{
    public static function fromArrayWithNonStringKeys(array $array): self
    {
        return new self('Non-string key in array found');
    }

    public static function fromNonNestedKey(mixed $key): self
    {
        return new self(sprintf('Expected string or nested integer key, but "%s" was provided', gettype($key)));
    }
}
