<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint\Exception;

use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
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
    public function fromNonNumericValueWithString(): void
    {
        self::assertSame(
            'Value was expected to be numeric, but got "test"',
            InvalidTypeException::fromNonNumericValue('test')->getMessage()
        );
    }

    #[Test]
    public function fromNonNumericValueWithObject(): void
    {
        self::assertSame(
            'Value was expected to be numeric, but got object',
            InvalidTypeException::fromNonNumericValue(new stdClass())->getMessage()
        );
    }
}
