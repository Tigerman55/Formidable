<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\ValidBindResultException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Exception\ValidBindResultException
 */
class ValidBindResultExceptionTest extends TestCase
{
    public function testFromGetFormErrorsAttempt()
    {
        self::assertSame(
            'Form errors can only be retrieved when bind result was not successful',
            ValidBindResultException::fromGetFormErrorsAttempt()->getMessage()
        );
    }
}
