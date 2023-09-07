<?php

declare(strict_types=1);

namespace Formidable\Mapping\Exception;

use DomainException;

use function gettype;
use function sprintf;

final class InvalidMappingKeyException extends DomainException implements ExceptionInterface
{
    public static function fromInvalidMappingKey(mixed $mappingKey): self
    {
        return new self(sprintf('Mapping key must be a nonempty string, but got %s', gettype($mappingKey)));
    }
}
