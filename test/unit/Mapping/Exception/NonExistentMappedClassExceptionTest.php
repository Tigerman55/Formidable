<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\NonExistentMappedClassException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NonExistentMappedClassException::class)]
class NonExistentMappedClassExceptionTest extends TestCase
{
    #[Test]
    public function fromNonExistentClass(): void
    {
        self::assertSame(
            'Class with name foo does not exist',
            NonExistentMappedClassException::fromNonExistentClass('foo')->getMessage()
        );
    }
}
