<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\EmailAddressConstraint;
use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmailAddressConstraint::class)]
class EmailAddressConstraintTest extends TestCase
{
    #[Test]
    public function assertionWithInvalidValueType(): void
    {
        $constraint = new EmailAddressConstraint();
        $this->expectException(InvalidTypeException::class);
        $constraint(1);
    }

    #[Test]
    public function failureWithEmptyString(): void
    {
        $constraint       = new EmailAddressConstraint();
        $validationResult = $constraint('');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['error.email-address' => []]);
    }

    #[Test]
    public function failureWithInvalidEmailAddress(): void
    {
        $constraint       = new EmailAddressConstraint();
        $validationResult = $constraint('foobar');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['error.email-address' => []]);
    }

    #[Test]
    public function successWithValidEmailAddress(): void
    {
        $constraint       = new EmailAddressConstraint();
        $validationResult = $constraint('foo@bar.com');
        self::assertTrue($validationResult->isSuccess());
    }
}
