<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\NotEmptyConstraint;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotEmptyConstraint::class)]
class NotEmptyConstraintTest extends TestCase
{
    #[Test]
    public function assertionWithInvalidValueType(): void
    {
        $constraint = new NotEmptyConstraint();
        $this->expectException(InvalidTypeException::class);
        $constraint(1);
    }

    #[Test]
    public function failureWithEmptyString(): void
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

    #[Test]
    public function successWithValidString(): void
    {
        $constraint       = new NotEmptyConstraint();
        $validationResult = $constraint('a');
        self::assertTrue($validationResult->isSuccess());
    }
}
