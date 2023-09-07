<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use Formidable\Exception\InvalidValueException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidValueException::class)]
class InvalidValueExceptionTest extends TestCase
{
    #[Test]
    public function fromArrayWithNonStringKeys(): void
    {
        self::assertSame(
            'Non-string value in array found',
            InvalidValueException::fromArrayWithNonStringValues([])->getMessage()
        );
    }

    #[Test]
    public function fromNonNestedKey(): void
    {
        self::assertSame(
            'Expected string or array value, but "boolean" was provided',
            InvalidValueException::fromNonNestedValue(true)->getMessage()
        );
    }
}
