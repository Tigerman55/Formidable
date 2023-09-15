<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint\Exception;

use Formidable\Mapping\Constraint\Exception\InvalidStepException;
use Litipk\BigNumbers\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

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
    public function fromNonNumericBaseWithString(): void
    {
        self::assertSame(
            'Base was expected to be numeric, but got "test"',
            InvalidStepException::fromNonNumericBase('test')->getMessage()
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
