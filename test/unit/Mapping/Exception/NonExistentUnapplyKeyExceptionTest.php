<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\NonExistentUnapplyKeyException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Exception\NonExistentUnapplyKeyException
 */
class NonExistentUnapplyKeyExceptionTest extends TestCase
{
    public function testFromNonExistentUnapplyKey()
    {
        self::assertSame(
            'Key "foo" not found in array returned by unapply function',
            NonExistentUnapplyKeyException::fromNonExistentUnapplyKey('foo')->getMessage()
        );
    }
}
