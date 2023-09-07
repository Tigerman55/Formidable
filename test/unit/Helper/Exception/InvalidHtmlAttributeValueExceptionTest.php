<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\InvalidHtmlAttributeValueException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\Exception\InvalidHtmlAttributeValueException
 */
class InvalidHtmlAttributeValueExceptionTest extends TestCase
{
    public function testFromInvalidValue()
    {
        self::assertSame(
            'HTML attribute value must be of type string, but got integer',
            InvalidHtmlAttributeValueException::fromInvalidValue(1)->getMessage()
        );
    }
}
