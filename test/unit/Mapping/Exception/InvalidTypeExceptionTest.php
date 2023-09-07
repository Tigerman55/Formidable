<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\InvalidTypeException;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

/**
 * @covers Formidable\Mapping\Exception\InvalidTypeException
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
}
