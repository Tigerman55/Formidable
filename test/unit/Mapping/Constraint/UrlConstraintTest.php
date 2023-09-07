<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use Formidable\Mapping\Constraint\UrlConstraint;
use Mapping\Constraint\ValidationErrorAssertion;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Constraint\UrlConstraint
 */
class UrlConstraintTest extends TestCase
{
    public function testAssertionWithInvalidValueType()
    {
        $constraint = new UrlConstraint();
        $this->expectException(InvalidTypeException::class);
        $constraint(1);
    }

    public function testFailureWithEmptyString()
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['error.url' => []]);
    }

    public function testFailureWithInvalidUrl()
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('foobar');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['error.url' => []]);
    }

    public function testSuccessWithValidHttpUrl()
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('http://example.com');
        self::assertTrue($validationResult->isSuccess());
    }

    public function testSuccessWithValidHttpUrlWithLocalhost()
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('http://localhost');
        self::assertTrue($validationResult->isSuccess());
    }

    public function testSuccessWithValidIrcUrl()
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('irc://example.com');
        self::assertTrue($validationResult->isSuccess());
    }
}
