<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Exception;
use Formidable\Mapping\Exception\BindFailureException;
use Formidable\Mapping\Exception\NestedMappingExceptionTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BindFailureException::class), CoversClass(NestedMappingExceptionTrait::class)]
class BindFailureExceptionTest extends TestCase
{
    #[Test]
    public function fromBindExceptionWithGenericException(): void
    {
        $previous  = new Exception('test');
        $exception = BindFailureException::fromBindException('foo', $previous);

        self::assertSame(
            'Failed to bind foo: test',
            $exception->getMessage()
        );
        self::assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function fromBindExceptionWithNestedBindFailureException(): void
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
