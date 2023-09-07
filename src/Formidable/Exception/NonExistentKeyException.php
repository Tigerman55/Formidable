<?php

declare(strict_types=1);

namespace Formidable\Exception;

use OutOfBoundsException;

use function sprintf;

final class NonExistentKeyException extends OutOfBoundsException implements ExceptionInterface
{
    public static function fromNonExistentKey(string $key): self
    {
        return new self(sprintf('Non-existent key "%s" provided', $key));
    }
}
