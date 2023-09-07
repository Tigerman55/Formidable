<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\ValidationResult;
use PHPUnit\Framework\TestCase;

class ValidationErrorAssertion
{
    public static function assertErrorMessages(
        TestCase $testCase,
        ValidationResult $validationResult,
        array $expectedMessages
    ): void {
        $actualMessages = [];

        foreach ($validationResult->getValidationErrors() as $validationError) {
            $actualMessages[$validationError->getMessage()] = $validationError->getArguments();
        }

        $testCase::assertSame($expectedMessages, $actualMessages);
    }
}
