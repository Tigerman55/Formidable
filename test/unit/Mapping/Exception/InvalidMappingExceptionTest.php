<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\InvalidMappingException;
use Formidable\Mapping\MappingInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

use function sprintf;

#[CoversClass(InvalidMappingException::class)]
class InvalidMappingExceptionTest extends TestCase
{
    #[Test]
    public function fromInvalidMappingWithObject(): void
    {
        self::assertSame(
            sprintf('Mapping was expected to implement %s, but got stdClass', MappingInterface::class),
            InvalidMappingException::fromInvalidMapping(new stdClass())->getMessage()
        );
    }

    #[Test]
    public function fromInvalidMappingWithScalar(): void
    {
        self::assertSame(
            sprintf('Mapping was expected to implement %s, but got boolean', MappingInterface::class),
            InvalidMappingException::fromInvalidMapping(true)->getMessage()
        );
    }
}
