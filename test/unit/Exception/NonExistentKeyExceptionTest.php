<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use Formidable\Exception\NonExistentKeyException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Exception\NonExistentKeyException
 */
class NonExistentKeyExceptionTest extends TestCase
{
    public function testFromNonExistentKey()
    {
        self::assertSame(
            'Non-existent key "foo" provided',
            NonExistentKeyException::fromNonExistentKey('foo')->getMessage()
        );
    }
}
