<?php

declare(strict_types=1);

namespace Formidable\Exception;

use DomainException;

final class InvalidKeyException extends DomainException implements ExceptionInterface
{
    public static function fromArrayWithNonStringKeys(): self
    {
        return new self('Non-string key in array found');
    }

    public static function fromNonNestedKey(): self
    {
        return new self('Expected string or nested integer key, but integer was provided');
    }
}
