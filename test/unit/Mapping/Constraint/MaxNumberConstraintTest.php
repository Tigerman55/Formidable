<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidLimitException;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use Formidable\Mapping\Constraint\MaxNumberConstraint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MaxNumberConstraint::class)]
class MaxNumberConstraintTest extends TestCase
{
    public static function validValueProvider(): array
    {
        return [
            [0, 0],
            [0, -1],
            [0., 0.],
            [0., -0.1],
            ['0', '0'],
            ['0', '-0.1'],
        ];
    }

    #[Test, DataProvider('validValueProvider')]
    public function validValues(int|float|string $limit, int|float|string $value): void
    {
        $constraint       = new MaxNumberConstraint($limit);
        $validationResult = $constraint($value);
        self::assertTrue($validationResult->isSuccess());
    }

    public static function invalidValueProvider(): array
    {
        return [
            [0, 1],
            [0., 1.],
            ['0', '1'],
        ];
    }

    #[Test, DataProvider('invalidValueProvider')]
    public function invalidValues(int|float|string $limit, int|float|string $value): void
    {
        $constraint       = new MaxNumberConstraint($limit);
        $validationResult = $constraint($value);
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages(
            $this,
            $validationResult,
            ['error.max-number' => ['limit' => (string) $limit]]
        );
    }

    #[Test]
    public function assertionWithInvalidLimitType(): void
    {
        $this->expectException(InvalidLimitException::class);
        new MaxNumberConstraint('test');
    }

    #[Test]
    public function assertionWithNonNumericValueType(): void
    {
        $constraint = new MaxNumberConstraint(0);
        $this->expectException(InvalidTypeException::class);
        $constraint('test');
    }
}
