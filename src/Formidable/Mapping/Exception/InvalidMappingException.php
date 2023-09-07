<?php

declare(strict_types=1);

namespace Formidable\Mapping\Exception;

use DomainException;
use Formidable\Mapping\MappingInterface;

use function gettype;
use function is_object;
use function sprintf;

final class InvalidMappingException extends DomainException implements ExceptionInterface
{
    public static function fromInvalidMapping(mixed $mapping): self
    {
        return new self(sprintf(
            'Mapping was expected to implement %s, but got %s',
            MappingInterface::class,
            is_object($mapping) ? $mapping::class : gettype($mapping)
        ));
    }
}
