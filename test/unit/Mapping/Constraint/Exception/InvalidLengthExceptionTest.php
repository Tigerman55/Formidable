<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint\Exception;

use Formidable\Mapping\Constraint\Exception\InvalidLengthException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidLengthException::class)]
class InvalidLengthExceptionTest extends TestCase
{
    #[Test]
    public function fromNegativeLength(): void
    {
        self::assertSame(
            'Length must be greater than or equal to zero, but got -1',
            InvalidLengthException::fromNegativeLength(-1)->getMessage()
        );
    }
}
