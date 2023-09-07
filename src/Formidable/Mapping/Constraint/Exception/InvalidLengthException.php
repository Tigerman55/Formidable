<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint\Exception;

use DomainException;

use function sprintf;

final class InvalidLengthException extends DomainException implements ExceptionInterface
{
    public static function fromNegativeLength(int $length): self
    {
        return new self(sprintf(
            'Length must be greater than or equal to zero, but got %d',
            $length
        ));
    }
}
