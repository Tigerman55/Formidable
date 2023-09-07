<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\MappedClassMismatchException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(MappedClassMismatchException::class)]
class MappedClassMismatchExceptionTest extends TestCase
{
    #[Test]
    public function fromMismatchedClassWithObject(): void
    {
        self::assertSame(
            'Value to bind or unbind must be an instance of foo, but got stdClass',
            MappedClassMismatchException::fromMismatchedClass('foo', new stdClass())->getMessage()
        );
    }

    #[Test]
    public function fromMismatchedClassWithScalar(): void
    {
        self::assertSame(
            'Value to bind or unbind must be an instance of foo, but got boolean',
            MappedClassMismatchException::fromMismatchedClass('foo', true)->getMessage()
        );
    }
}
