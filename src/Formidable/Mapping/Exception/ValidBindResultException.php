<?php

declare(strict_types=1);

namespace Formidable\Mapping\Exception;

use DomainException;

final class ValidBindResultException extends DomainException implements ExceptionInterface
{
    public static function fromGetFormErrorsAttempt(): self
    {
        return new self('Form errors can only be retrieved when bind result was not successful');
    }
}
