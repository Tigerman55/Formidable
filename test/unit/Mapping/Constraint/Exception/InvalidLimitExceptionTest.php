<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint\Exception;

use Formidable\Mapping\Constraint\Exception\InvalidLimitException;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

/**
 * @covers Formidable\Mapping\Constraint\Exception\InvalidLimitException
 */
class InvalidLimitExceptionTest extends TestCase
{
    public function testFromNonNumericValueWithString()
    {
        self::assertSame(
            'Limit was expected to be numeric, but got "test"',
            InvalidLimitException::fromNonNumericValue('test')->getMessage()
        );
    }

    public function testFromNonNumericValueWithObject()
    {
        self::assertSame(
            'Limit was expected to be numeric, but got object',
            InvalidLimitException::fromNonNumericValue(new stdClass())->getMessage()
        );
    }
}
