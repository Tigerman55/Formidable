<?php

declare(strict_types=1);

namespace Formidable\Helper\Exception;

use DomainException;

final class InvalidHtmlAttributeKeyException extends DomainException implements ExceptionInterface
{
    public static function fromInvalidKey(): self
    {
        return new self('HTML attribute key must be of type string, but got integer');
    }
}
