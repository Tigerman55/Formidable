<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\ValidationError;
use Formidable\Mapping\Constraint\ValidationResult;
use Mapping\Constraint\ValidationErrorAssertion;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Constraint\ValidationResult
 */
class ValidationResultTest extends TestCase
{
    public function testSuccessWithoutErrors()
    {
        self::assertTrue((new ValidationResult())->isSuccess());
    }

    public function testFailureWithErrors()
    {
        self::assertFalse((new ValidationResult(new ValidationError('')))->isSuccess());
    }

    public function testValidationErrorsRetrieval()
    {
        $validationResult = new ValidationResult(new ValidationError('foo'), new ValidationError('bar'));
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['foo' => [], 'bar' => []]);
    }

    public function testMerge()
    {
        $validationResultA = new ValidationResult(new ValidationError('foo'));
        $validationResultB = new ValidationResult();
        $validationResultC = new ValidationResult(new ValidationError('bar'), new ValidationError('baz'));

        $validationResult = $validationResultA->merge($validationResultB)->merge($validationResultC);
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages(
            $this,
            $validationResult,
            ['foo' => [], 'bar' => [], 'baz' => []]
        );
    }
}
