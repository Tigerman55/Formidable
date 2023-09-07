<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use Formidable\Exception\InvalidKeyException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Exception\InvalidKeyException
 */
class InvalidKeyExceptionTest extends TestCase
{
    public function testFromArrayWithNonStringKeys()
    {
        self::assertSame(
            'Non-string key in array found',
            InvalidKeyException::fromArrayWithNonStringKeys([])->getMessage()
        );
    }

    public function testFromNonNestedKey()
    {
        self::assertSame(
            'Expected string or nested integer key, but "boolean" was provided',
            InvalidKeyException::fromNonNestedKey(true)->getMessage()
        );
    }
}
