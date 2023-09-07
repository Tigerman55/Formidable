<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\InvalidHtmlAttributeKeyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidHtmlAttributeKeyException::class)]
class InvalidHtmlAttributeKeyExceptionTest extends TestCase
{
    #[Test]
    public function fromInvalidKey(): void
    {
        self::assertSame(
            'HTML attribute key must be of type string, but got integer',
            InvalidHtmlAttributeKeyException::fromInvalidKey(1)->getMessage()
        );
    }
}
