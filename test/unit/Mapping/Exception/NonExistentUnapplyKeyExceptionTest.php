<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\NonExistentUnapplyKeyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NonExistentUnapplyKeyException::class)]
class NonExistentUnapplyKeyExceptionTest extends TestCase
{
    #[Test]
    public function fromNonExistentUnapplyKey(): void
    {
        self::assertSame(
            'Key "foo" not found in array returned by unapply function',
            NonExistentUnapplyKeyException::fromNonExistentUnapplyKey('foo')->getMessage()
        );
    }
}
