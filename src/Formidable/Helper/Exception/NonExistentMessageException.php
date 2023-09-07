<?php

declare(strict_types=1);

namespace Formidable\Helper\Exception;

use OutOfBoundsException;

use function sprintf;

final class NonExistentMessageException extends OutOfBoundsException implements ExceptionInterface
{
    public static function fromNonExistentMessageKey(string $key): self
    {
        return new self(sprintf('Non-existent message key "%s" provided', $key));
    }
}
