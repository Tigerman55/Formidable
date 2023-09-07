<?php

declare(strict_types=1);

namespace Formidable\Helper\Exception;

use DomainException;

use function gettype;
use function sprintf;

final class InvalidHtmlAttributeKeyException extends DomainException implements ExceptionInterface
{
    public static function fromInvalidKey(mixed $key): self
    {
        return new self(sprintf('HTML attribute key must be of type string, but got %s', gettype($key)));
    }
}
