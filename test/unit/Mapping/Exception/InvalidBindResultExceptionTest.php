<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\InvalidBindResultException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidBindResultException::class)]
class InvalidBindResultExceptionTest extends TestCase
{
    #[Test]
    public function fromGetValueAttempt(): void
    {
        self::assertSame(
            'Value can only be retrieved when bind result was successful',
            InvalidBindResultException::fromGetValueAttempt()->getMessage()
        );
    }
}
