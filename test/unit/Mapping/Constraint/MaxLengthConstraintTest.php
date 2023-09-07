<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidLengthException;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use Formidable\Mapping\Constraint\MaxLengthConstraint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MaxLengthConstraint::class)]
class MaxLengthConstraintTest extends TestCase
{
    #[Test]
    public function assertionWithInvalidLength(): void
    {
        $this->expectException(InvalidLengthException::class);
        new MaxLengthConstraint(-1);
    }

    #[Test]
    public function assertionWithInvalidValueType(): void
    {
        $constraint = new MaxLengthConstraint(0);
        $this->expectException(InvalidTypeException::class);
        $constraint(1);
    }

    #[Test]
    public function failureWithEmptyString(): void
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

    #[Test]
    public function successWithMultiByte(): void
    {
        $constraint       = new MaxLengthConstraint(1);
        $validationResult = $constraint('ü');
        self::assertTrue($validationResult->isSuccess());
    }

    #[Test]
    public function successWithValidString(): void
    {
        $constraint       = new MaxLengthConstraint(2);
        $validationResult = $constraint('ab');
        self::assertTrue($validationResult->isSuccess());
    }
}
