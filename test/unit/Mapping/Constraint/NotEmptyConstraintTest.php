<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\NotEmptyConstraint;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use Mapping\Constraint\ValidationErrorAssertion;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Constraint\NotEmptyConstraint
 */
class NotEmptyConstraintTest extends TestCase
{
    public function testAssertionWithInvalidValueType()
    {
        $constraint = new NotEmptyConstraint();
        $this->expectException(InvalidTypeException::class);
        $constraint(1);
    }

    public function testFailureWithEmptyString()
    {
        $constraint       = new NotEmptyConstraint();
        $validationResult = $constraint('');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages(
            $this,
            $validationResult,
            ['error.empty' => []]
        );
    }

    public function testSuccessWithValidString()
    {
        $constraint       = new NotEmptyConstraint();
        $validationResult = $constraint('a');
        self::assertTrue($validationResult->isSuccess());
    }
}
