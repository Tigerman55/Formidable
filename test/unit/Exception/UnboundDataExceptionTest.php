<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use Formidable\Exception\UnboundDataException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Exception\UnboundDataException
 */
class UnboundDataExceptionTest extends TestCase
{
    public function testFromGetValueAttempt()
    {
        self::assertSame(
            'No data have been bound to the form',
            UnboundDataException::fromGetValueAttempt()->getMessage()
        );
    }
}
