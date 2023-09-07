<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\MappedClassMismatchException;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

/**
 * @covers Formidable\Mapping\Exception\MappedClassMismatchException
 */
class MappedClassMismatchExceptionTest extends TestCase
{
    public function testFromMismatchedClassWithObject()
    {
        self::assertSame(
            'Value to bind or unbind must be an instance of foo, but got stdClass',
            MappedClassMismatchException::fromMismatchedClass('foo', new stdClass())->getMessage()
        );
    }

    public function testFromMismatchedClassWithScalar()
    {
        self::assertSame(
            'Value to bind or unbind must be an instance of foo, but got boolean',
            MappedClassMismatchException::fromMismatchedClass('foo', true)->getMessage()
        );
    }
}
