<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint\Exception;

use DomainException;

use function sprintf;

final class InvalidLimitException extends DomainException implements ExceptionInterface
{
    public static function fromNonNumericValue(string $actualValue): self
    {
        return new self(sprintf('Limit was expected to be numeric, but got "%s"', $actualValue));
    }
}
