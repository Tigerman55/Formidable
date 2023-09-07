<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use Formidable\Exception\InvalidKeyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidKeyException::class)]
class InvalidKeyExceptionTest extends TestCase
{
    #[Test]
    public function fromArrayWithNonStringKeys(): void
    {
        self::assertSame(
            'Non-string key in array found',
            InvalidKeyException::fromArrayWithNonStringKeys([])->getMessage()
        );
    }

    #[Test]
    public function fromNonNestedKey(): void
    {
        self::assertSame(
            'Expected string or nested integer key, but "boolean" was provided',
            InvalidKeyException::fromNonNestedKey(true)->getMessage()
        );
    }
}
