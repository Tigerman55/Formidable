<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\InvalidSelectLabelException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\Exception\InvalidSelectLabelException
 */
class InvalidSelectLabelExceptionTest extends TestCase
{
    public function testFromInvalidLabel()
    {
        self::assertSame(
            'Label must either be a string or an array of child values, but got integer',
            InvalidSelectLabelException::fromInvalidLabel(1)->getMessage()
        );
    }
}
