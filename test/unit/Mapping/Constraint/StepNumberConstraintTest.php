<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidStepException;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use Formidable\Mapping\Constraint\StepNumberConstraint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(StepNumberConstraint::class)]
class StepNumberConstraintTest extends TestCase
{
    public static function validStepProvider(): array
    {
        return [
            // Integers
            [1, null, 0],
            [1, null, -1],
            [1, null, 1],
            [2, 1, 3],
            [2, 1, -1],

            // Floats
            [0.7, null, 0.7],
            [0.7, 0.3, 1.],
            [0.7, 0.3, -0.4],

            // Decimals
            ['0.7', null, '0.7'],
            ['0.7', '0.3', '1'],
            ['5', '2', '7'],
            ['5', '2', '-3'],
        ];
    }

    #[Test, DataProvider('validStepProvider')]
    public function validSteps(int|float|string $step, int|float|string|null $base, int|float|string $value): void
    {
        $constraint = new StepNumberConstraint($step, $base);
        self::assertTrue($constraint($value)->isSuccess());
    }

    public static function invalidStepProvider(): array
    {
        return [
            // Integers
            [2, -1, 0, '-1', '1'],
            [2, null, -1, '-2', '0'],
            [2, null, 1, '0', '2'],

            // Floats
            [0.7, null, 0.35, '0', '0.7'],
            [0.7, null, 0.71, '0.7', '1.4'],
            [0.7, null, 0.70000000000001, '0.7', '1.4'],

            // Decimals
            ['0.7', null, '0.35', '0', '0.7'],
            ['0.7', null, '0.71', '0.7', '1.4'],
        ];
    }

    #[Test, DataProvider('invalidStepProvider')]
    public function invalidSteps(
        int|float|string $step,
        int|float|string|null $base,
        int|float|string $value,
        string $lowValue,
        string $highValue
    ): void {
        $constraint       = new StepNumberConstraint($step, $base);
        $validationResult = $constraint($value);
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages(
            $this,
            $validationResult,
            ['error.step-number' => ['lowValue' => $lowValue, 'highValue' => $highValue]]
        );
    }

    #[Test]
    public function assertionWithNegativeIntegerStep(): void
    {
        $this->expectException(InvalidStepException::class);
        new StepNumberConstraint(-1);
    }

    #[Test]
    public function assertionWithZeroIntegerStep(): void
    {
        $this->expectException(InvalidStepException::class);
        new StepNumberConstraint(0);
    }

    #[Test]
    public function assertionWithNegativeFloatStep(): void
    {
        $this->expectException(InvalidStepException::class);
        new StepNumberConstraint(-1.);
    }

    #[Test]
    public function assertionWithZeroFloatStep(): void
    {
        $this->expectException(InvalidStepException::class);
        new StepNumberConstraint(0.);
    }

    #[Test]
    public function assertionWithNegativeDecimalStep(): void
    {
        $this->expectException(InvalidStepException::class);
        new StepNumberConstraint('-1');
    }

    #[Test]
    public function assertionWithZeroDecimalStep(): void
    {
        $this->expectException(InvalidStepException::class);
        new StepNumberConstraint('0');
    }

    #[Test]
    public function assertionWithNonNumericStep(): void
    {
        $this->expectException(InvalidStepException::class);
        new StepNumberConstraint('test');
    }

    #[Test]
    public function assertionWithNonNumericBase(): void
    {
        $this->expectException(InvalidStepException::class);
        new StepNumberConstraint(1, 'test');
    }

    #[Test]
    public function assertionWithNonNumericValue(): void
    {
        $constraint = new StepNumberConstraint(1);
        $this->expectException(InvalidTypeException::class);
        $constraint('test');
    }
}
