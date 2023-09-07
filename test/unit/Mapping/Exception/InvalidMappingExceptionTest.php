<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\InvalidMappingException;
use Formidable\Mapping\MappingInterface;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

use function sprintf;

/**
 * @covers Formidable\Mapping\Exception\InvalidMappingException
 */
class InvalidMappingExceptionTest extends TestCase
{
    public function testFromInvalidMappingWithObject()
    {
        self::assertSame(
            sprintf('Mapping was expected to implement %s, but got stdClass', MappingInterface::class),
            InvalidMappingException::fromInvalidMapping(new stdClass())->getMessage()
        );
    }

    public function testFromInvalidMappingWithScalar()
    {
        self::assertSame(
            sprintf('Mapping was expected to implement %s, but got boolean', MappingInterface::class),
            InvalidMappingException::fromInvalidMapping(true)->getMessage()
        );
    }
}
