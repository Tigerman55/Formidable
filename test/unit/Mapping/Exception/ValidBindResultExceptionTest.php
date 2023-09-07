<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\ValidBindResultException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidBindResultException::class)]
class ValidBindResultExceptionTest extends TestCase
{
    #[Test]
    public function fromGetFormErrorsAttempt(): void
    {
        self::assertSame(
            'Form errors can only be retrieved when bind result was not successful',
            ValidBindResultException::fromGetFormErrorsAttempt()->getMessage()
        );
    }
}
