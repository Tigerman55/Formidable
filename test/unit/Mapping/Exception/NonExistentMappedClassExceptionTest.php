<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\NonExistentMappedClassException;
use Formidable\Mapping\MappingInterface;
use PHPUnit_Framework_TestCase as TestCase;

use function sprintf;

/**
 * @covers Formidable\Mapping\Exception\NonExistentMappedClassException
 */
class NonExistentMappedClassExceptionTest extends TestCase
{
    public function testFromNonExistentClass()
    {
        self::assertSame(
            sprintf('Class with name foo does not exist', MappingInterface::class),
            NonExistentMappedClassException::fromNonExistentClass('foo')->getMessage()
        );
    }
}
