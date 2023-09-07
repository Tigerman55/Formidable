<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter\Exception;

use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(InvalidTypeException::class)]
class InvalidTypeExceptionTest extends TestCase
{
    #[Test]
    public function fromInvalidTypeWithObject(): void
    {
        self::assertSame(
            'Value was expected to be of type foo, but got stdClass',
            InvalidTypeException::fromInvalidType(new stdClass(), 'foo')->getMessage()
        );
    }

    #[Test]
    public function fromInvalidTypeWithScalar(): void
    {
        self::assertSame(
            'Value was expected to be of type foo, but got boolean',
            InvalidTypeException::fromInvalidType(true, 'foo')->getMessage()
        );
    }

    #[Test]
    public function fromNonNumericString(): void
    {
        self::assertSame(
            'String was expected to be numeric, but got "test"',
            InvalidTypeException::fromNonNumericString('test')->getMessage()
        );
    }
}
