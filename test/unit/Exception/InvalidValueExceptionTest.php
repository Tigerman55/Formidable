<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use Formidable\Exception\InvalidValueException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Exception\InvalidValueException
 */
class InvalidValueExceptionTest extends TestCase
{
    public function testFromArrayWithNonStringKeys()
    {
        self::assertSame(
            'Non-string value in array found',
            InvalidValueException::fromArrayWithNonStringValues([])->getMessage()
        );
    }

    public function testFromNonNestedKey()
    {
        self::assertSame(
            'Expected string or array value, but "boolean" was provided',
            InvalidValueException::fromNonNestedValue(true)->getMessage()
        );
    }
}
