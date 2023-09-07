<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint\Exception;

use Formidable\Mapping\Constraint\Exception\InvalidStepException;
use Litipk\BigNumbers\Decimal;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

/**
 * @covers Formidable\Mapping\Constraint\Exception\InvalidStepException
 */
class InvalidStepExceptionTest extends TestCase
{
    public function testFromNonNumericStepWithString()
    {
        self::assertSame(
            'Step was expected to be numeric, but got "test"',
            InvalidStepException::fromNonNumericStep('test')->getMessage()
        );
    }

    public function testFromNonNumericStepWithObject()
    {
        self::assertSame(
            'Step was expected to be numeric, but got object',
            InvalidStepException::fromNonNumericStep(new stdClass())->getMessage()
        );
    }

    public function testFromNonNumericBaseWithString()
    {
        self::assertSame(
            'Base was expected to be numeric, but got "test"',
            InvalidStepException::fromNonNumericBase('test')->getMessage()
        );
    }

    public function testFromNonNumericBaseWithObject()
    {
        self::assertSame(
            'Base was expected to be numeric, but got object',
            InvalidStepException::fromNonNumericBase(new stdClass())->getMessage()
        );
    }

    public function testFromZeroOrNegativeStep()
    {
        self::assertSame(
            'Step must be greater than zero, but got 0',
            InvalidStepException::fromZeroOrNegativeStep(Decimal::fromInteger(0))->getMessage()
        );
    }
}
