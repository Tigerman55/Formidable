<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\InvalidUnapplyResultException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(InvalidUnapplyResultException::class)]
class InvalidUnapplyResultExceptionTest extends TestCase
{
    #[Test]
    public function fromInvalidUnapplyResult(): void
    {
        self::assertSame(
            'Unapply was expected to return an array, but returned object',
            InvalidUnapplyResultException::fromInvalidUnapplyResult(new stdClass())->getMessage()
        );
    }
}
