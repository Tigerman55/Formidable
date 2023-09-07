<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint\Exception;

use Formidable\Mapping\Constraint\Exception\InvalidStepException;
use Litipk\BigNumbers\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(InvalidStepException::class)]
class InvalidStepExceptionTest extends TestCase
{
    #[Test]
    public function fromNonNumericStepWithString(): void
    {
        self::assertSame(
            'Step was expected to be numeric, but got "test"',
            InvalidStepException::fromNonNumericStep('test')->getMessage()
        );
    }

    #[Test]
    public function fromNonNumericStepWithObject(): void
    {
        self::assertSame(
            'Step was expected to be numeric, but got object',
            InvalidStepException::fromNonNumericStep(new stdClass())->getMessage()
        );
    }

    #[Test]
    public function fromNonNumericBaseWithString(): void
    {
        self::assertSame(
            'Base was expected to be numeric, but got "test"',
            InvalidStepException::fromNonNumericBase('test')->getMessage()
        );
    }

    #[Test]
    public function fromNonNumericBaseWithObject(): void
    {
        self::assertSame(
            'Base was expected to be numeric, but got object',
            InvalidStepException::fromNonNumericBase(new stdClass())->getMessage()
        );
    }

    #[Test]
    public function fromZeroOrNegativeStep(): void
    {
        self::assertSame(
            'Step must be greater than zero, but got 0',
            InvalidStepException::fromZeroOrNegativeStep(Decimal::fromInteger(0))->getMessage()
        );
    }
}
