<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidLengthException;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use Formidable\Mapping\Constraint\MaxLengthConstraint;
use Mapping\Constraint\ValidationErrorAssertion;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Constraint\MaxLengthConstraint
 */
class MaxLengthConstraintTest extends TestCase
{
    public function testAssertionWithInvalidLength()
    {
        $this->expectException(InvalidLengthException::class);
        new MaxLengthConstraint(-1);
    }

    public function testAssertionWithInvalidValueType()
    {
        $constraint = new MaxLengthConstraint(0);
        $this->expectException(InvalidTypeException::class);
        $constraint(1);
    }

    public function testFailureWithEmptyString()
    {
        $constraint       = new MaxLengthConstraint(1);
        $validationResult = $constraint('ab');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages(
            $this,
            $validationResult,
            ['error.max-length' => ['lengthLimit' => 1]]
        );
    }

    public function testSuccessWithMultiByte()
    {
        $constraint       = new MaxLengthConstraint(1);
        $validationResult = $constraint('ü');
        self::assertTrue($validationResult->isSuccess());
    }

    public function testSuccessWithValidString()
    {
        $constraint       = new MaxLengthConstraint(2);
        $validationResult = $constraint('ab');
        self::assertTrue($validationResult->isSuccess());
    }
}
