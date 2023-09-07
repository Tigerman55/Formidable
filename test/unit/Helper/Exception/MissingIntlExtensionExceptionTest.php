<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\MissingIntlExtensionException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\Exception\MissingIntlExtensionException
 */
class MissingIntlExtensionExceptionTest extends TestCase
{
    public function testFromMissingExtension()
    {
        self::assertSame(
            'You must install the PHP intl extension for this helper to work',
            MissingIntlExtensionException::fromMissingExtension()->getMessage()
        );
    }
}
