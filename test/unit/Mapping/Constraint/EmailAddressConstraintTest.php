<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\EmailAddressConstraint;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use Mapping\Constraint\ValidationErrorAssertion;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Constraint\EmailAddressConstraint
 */
class EmailAddressConstraintTest extends TestCase
{
    public function testAssertionWithInvalidValueType()
    {
        $constraint = new EmailAddressConstraint();
        $this->expectException(InvalidTypeException::class);
        $constraint(1);
    }

    public function testFailureWithEmptyString()
    {
        $constraint       = new EmailAddressConstraint();
        $validationResult = $constraint('');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['error.email-address' => []]);
    }

    public function testFailureWithInvalidEmailAddress()
    {
        $constraint       = new EmailAddressConstraint();
        $validationResult = $constraint('foobar');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['error.email-address' => []]);
    }

    public function testSuccessWithValidEmailAddress()
    {
        $constraint       = new EmailAddressConstraint();
        $validationResult = $constraint('foo@bar.com');
        self::assertTrue($validationResult->isSuccess());
    }
}
