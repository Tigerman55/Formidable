<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\InvalidBindResultException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Exception\InvalidBindResultException
 */
class InvalidBindResultExceptionTest extends TestCase
{
    public function testFromGetValueAttempt()
    {
        self::assertSame(
            'Value can only be retrieved when bind result was successful',
            InvalidBindResultException::fromGetValueAttempt()->getMessage()
        );
    }
}
