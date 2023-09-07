<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\NonExistentMessageException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NonExistentMessageException::class)]
class NonExistentMessageExceptionTest extends TestCase
{
    #[Test]
    public function fromNonExistentMessageKey(): void
    {
        self::assertSame(
            'Non-existent message key "foo" provided',
            NonExistentMessageException::fromNonExistentMessageKey('foo')->getMessage()
        );
    }
}
