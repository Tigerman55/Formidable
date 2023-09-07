<?php

declare(strict_types=1);

namespace Formidable\Mapping\Exception;

use UnexpectedValueException;

use function gettype;
use function sprintf;

final class InvalidUnapplyResultException extends UnexpectedValueException implements ExceptionInterface
{
    public static function fromInvalidUnapplyResult(mixed $values): self
    {
        return new self(sprintf(
            'Unapply was expected to return an array, but returned %s',
            gettype($values)
        ));
    }
}
