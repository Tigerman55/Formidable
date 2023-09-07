<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\InvalidHtmlAttributeValueException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidHtmlAttributeValueException::class)]
class InvalidHtmlAttributeValueExceptionTest extends TestCase
{
    #[Test]
    public function fromInvalidValue(): void
    {
        self::assertSame(
            'HTML attribute value must be of type string, but got integer',
            InvalidHtmlAttributeValueException::fromInvalidValue(1)->getMessage()
        );
    }
}
