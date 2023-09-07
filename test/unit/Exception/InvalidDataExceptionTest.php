<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use Formidable\Exception\InvalidDataException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidDataException::class)]
class InvalidDataExceptionTest extends TestCase
{
    public function fromGetValueAttempt(): void
    {
        self::assertSame(
            'Value cannot be retrieved when the form has errors',
            InvalidDataException::fromGetValueAttempt()->getMessage()
        );
    }
}
