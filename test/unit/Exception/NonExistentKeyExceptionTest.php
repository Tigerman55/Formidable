<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use Formidable\Exception\NonExistentKeyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NonExistentKeyException::class)]
class NonExistentKeyExceptionTest extends TestCase
{
    #[Test]
    public function fromNonExistentKey(): void
    {
        self::assertSame(
            'Non-existent key "foo" provided',
            NonExistentKeyException::fromNonExistentKey('foo')->getMessage()
        );
    }
}
