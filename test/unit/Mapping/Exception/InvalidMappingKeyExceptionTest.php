<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\InvalidMappingKeyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(InvalidMappingKeyException::class)]
class InvalidMappingKeyExceptionTest extends TestCase
{
    #[Test]
    public function fromInvalidMappingKey(): void
    {
        self::assertSame(
            'Mapping key must be a nonempty string, but got object',
            InvalidMappingKeyException::fromInvalidMappingKey(new stdClass())->getMessage()
        );
    }
}
