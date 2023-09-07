<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use Formidable\Exception\UnboundDataException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnboundDataException::class)]
class UnboundDataExceptionTest extends TestCase
{
    #[Test]
    public function fromGetValueAttempt(): void
    {
        self::assertSame(
            'No data have been bound to the form',
            UnboundDataException::fromGetValueAttempt()->getMessage()
        );
    }
}
