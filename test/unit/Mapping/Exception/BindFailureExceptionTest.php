<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Exception;
use Formidable\Mapping\Exception\BindFailureException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Exception\BindFailureException
 * @covers Formidable\Mapping\Exception\NestedMappingExceptionTrait
 */
class BindFailureExceptionTest extends TestCase
{
    public function testFromBindExceptionWithGenericException()
    {
        $previous  = new Exception('test');
        $exception = BindFailureException::fromBindException('foo', $previous);

        self::assertSame(
            'Failed to bind foo: test',
            $exception->getMessage()
        );
        self::assertSame($previous, $exception->getPrevious());
    }

    public function testFromBindExceptionWithNestedBindFailureException()
    {
        $previous  = BindFailureException::fromBindException(
            'bar',
            BindFailureException::fromBindException('baz', new Exception('test'))
        );
        $exception = BindFailureException::fromBindException('foo', $previous);

        self::assertSame(
            'Failed to bind foo.bar.baz: test',
            $exception->getMessage()
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
