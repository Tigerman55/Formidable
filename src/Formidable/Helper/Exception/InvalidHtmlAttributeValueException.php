<?php

declare(strict_types=1);

namespace Formidable\Helper\Exception;

use DomainException;

use function gettype;
use function sprintf;

final class InvalidHtmlAttributeValueException extends DomainException implements ExceptionInterface
{
    public static function fromInvalidValue(mixed $value): self
    {
        return new self(sprintf('HTML attribute value must be of type string, but got %s', gettype($value)));
    }
}
