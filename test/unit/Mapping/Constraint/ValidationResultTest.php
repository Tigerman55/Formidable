<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\ValidationError;
use Formidable\Mapping\Constraint\ValidationResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidationResult::class)]
class ValidationResultTest extends TestCase
{
    #[Test]
    public function successWithoutErrors(): void
    {
        self::assertTrue((new ValidationResult())->isSuccess());
    }

    #[Test]
    public function failureWithErrors(): void
    {
        self::assertFalse((new ValidationResult(new ValidationError('')))->isSuccess());
    }

    #[Test]
    public function validationErrorsRetrieval(): void
    {
        $validationResult = new ValidationResult(new ValidationError('foo'), new ValidationError('bar'));
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['foo' => [], 'bar' => []]);
    }

    #[Test]
    public function merge(): void
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
