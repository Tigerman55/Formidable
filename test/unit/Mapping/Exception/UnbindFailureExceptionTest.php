<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Exception;
use Formidable\Mapping\Exception\UnbindFailureException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Exception\UnbindFailureException
 * @covers Formidable\Mapping\Exception\NestedMappingExceptionTrait
 */
class UnbindFailureExceptionTest extends TestCase
{
    public function testFromUnbindExceptionWithGenericException()
    {
        $previous  = new Exception('test');
        $exception = UnbindFailureException::fromUnbindException('foo', $previous);

        self::assertSame(
            'Failed to unbind foo: test',
            $exception->getMessage()
        );
        self::assertSame($previous, $exception->getPrevious());
    }

    public function testFromUnbindExceptionWithNestedBindFailureException()
    {
        $previous  = UnbindFailureException::fromUnbindException(
            'bar',
            UnbindFailureException::fromUnbindException('baz', new Exception('test'))
        );
        $exception = UnbindFailureException::fromUnbindException('foo', $previous);

        self::assertSame(
            'Failed to unbind foo.bar.baz: test',
            $exception->getMessage()
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
