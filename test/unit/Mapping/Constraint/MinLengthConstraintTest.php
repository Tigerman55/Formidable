<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidLengthException;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use Formidable\Mapping\Constraint\MinLengthConstraint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MinLengthConstraint::class)]
class MinLengthConstraintTest extends TestCase
{
    #[Test]
    public function assertionWithInvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        new MinLengthConstraint(-1);
    }

    #[Test]
    public function assertionWithInvalidValueType(): void
    {
        $constraint = new MinLengthConstraint(0);
        $this->expectException(InvalidTypeException::class);
        $constraint(1);
    }

    #[Test]
    public function failureWithEmptyString(): void
    {
        $constraint       = new MinLengthConstraint(1);
        $validationResult = $constraint('');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages(
            $this,
            $validationResult,
            ['error.min-length' => ['lengthLimit' => 1]]
        );
    }

    #[Test]
    public function failureWithMultiByte(): void
    {
        $constraint       = new MinLengthConstraint(2);
        $validationResult = $constraint('ü');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages(
            $this,
            $validationResult,
            ['error.min-length' => ['lengthLimit' => 2]]
        );
    }

    #[Test]
    public function successWithValidString(): void
    {
        $constraint       = new MinLengthConstraint(2);
        $validationResult = $constraint('ab');
        self::assertTrue($validationResult->isSuccess());
    }
}
