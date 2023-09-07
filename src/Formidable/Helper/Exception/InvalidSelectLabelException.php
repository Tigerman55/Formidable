<?php

declare(strict_types=1);

namespace Formidable\Helper\Exception;

use DomainException;

use function gettype;
use function sprintf;

final class InvalidSelectLabelException extends DomainException implements ExceptionInterface
{
    public static function fromInvalidLabel(mixed $label): self
    {
        return new self(sprintf(
            'Label must either be a string or an array of child values, but got %s',
            gettype($label)
        ));
    }
}
