<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\InvalidHtmlAttributeKeyException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\Exception\InvalidHtmlAttributeKeyException
 */
class InvalidHtmlAttributeKeyExceptionTest extends TestCase
{
    public function testFromInvalidKey()
    {
        self::assertSame(
            'HTML attribute key must be of type string, but got integer',
            InvalidHtmlAttributeKeyException::fromInvalidKey(1)->getMessage()
        );
    }
}
