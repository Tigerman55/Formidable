<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\InvalidMappingKeyException;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

/**
 * @covers Formidable\Mapping\Exception\InvalidMappingKeyException
 */
class InvalidMappingKeyExceptionTest extends TestCase
{
    public function testFromInvalidMappingKey()
    {
        self::assertSame(
            'Mapping key must be of type string, but got object',
            InvalidMappingKeyException::fromInvalidMappingKey(new stdClass())->getMessage()
        );
    }
}
