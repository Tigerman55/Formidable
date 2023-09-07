<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Exception;
use Formidable\Mapping\Exception\NestedMappingExceptionTrait;
use Formidable\Mapping\Exception\UnbindFailureException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnbindFailureException::class), CoversClass(NestedMappingExceptionTrait::class)]
class UnbindFailureExceptionTest extends TestCase
{
    #[Test]
    public function fromUnbindExceptionWithGenericException(): void
    {
        $previous  = new Exception('test');
        $exception = UnbindFailureException::fromUnbindException('foo', $previous);

        self::assertSame(
            'Failed to unbind foo: test',
            $exception->getMessage()
        );
        self::assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function fromUnbindExceptionWithNestedBindFailureException(): void
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
