<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\NonExistentMessageException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\Exception\NonExistentMessageException
 */
class NonExistentMessageExceptionTest extends TestCase
{
    public function testFromNonExistentMessageKey()
    {
        self::assertSame(
            'Non-existent message key "foo" provided',
            NonExistentMessageException::fromNonExistentMessageKey('foo')->getMessage()
        );
    }
}
