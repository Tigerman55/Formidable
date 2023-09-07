<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\Exception\InvalidTypeException;
use Formidable\Mapping\Constraint\UrlConstraint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UrlConstraint::class)]
class UrlConstraintTest extends TestCase
{
    #[Test]
    public function assertionWithInvalidValueType(): void
    {
        $constraint = new UrlConstraint();
        $this->expectException(InvalidTypeException::class);
        $constraint(1);
    }

    #[Test]
    public function failureWithEmptyString(): void
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['error.url' => []]);
    }

    #[Test]
    public function failureWithInvalidUrl(): void
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('foobar');
        self::assertFalse($validationResult->isSuccess());
        ValidationErrorAssertion::assertErrorMessages($this, $validationResult, ['error.url' => []]);
    }

    #[Test]
    public function successWithValidHttpUrl(): void
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('http://example.com');
        self::assertTrue($validationResult->isSuccess());
    }

    #[Test]
    public function successWithValidHttpUrlWithLocalhost(): void
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('http://localhost');
        self::assertTrue($validationResult->isSuccess());
    }

    #[Test]
    public function successWithValidIrcUrl(): void
    {
        $constraint       = new UrlConstraint();
        $validationResult = $constraint('irc://example.com');
        self::assertTrue($validationResult->isSuccess());
    }
}
