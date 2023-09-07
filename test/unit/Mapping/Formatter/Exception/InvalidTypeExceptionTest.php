<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter\Exception;

use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

/**
 * @covers Formidable\Mapping\Formatter\Exception\InvalidTypeException
 */
class InvalidTypeExceptionTest extends TestCase
{
    public function testFromInvalidTypeWithObject()
    {
        self::assertSame(
            'Value was expected to be of type foo, but got stdClass',
            InvalidTypeException::fromInvalidType(new stdClass(), 'foo')->getMessage()
        );
    }

    public function testFromInvalidTypeWithScalar()
    {
        self::assertSame(
            'Value was expected to be of type foo, but got boolean',
            InvalidTypeException::fromInvalidType(true, 'foo')->getMessage()
        );
    }

    public function testFromNonNumericString()
    {
        self::assertSame(
            'String was expected to be numeric, but got "test"',
            InvalidTypeException::fromNonNumericString('test')->getMessage()
        );
    }
}
